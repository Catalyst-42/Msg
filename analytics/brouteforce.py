import requests
import logging
import os
import re
import random
from datetime import datetime
from urllib.parse import unquote, urlparse

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
    handlers=[logging.StreamHandler()]
)

# Configuration
URL = "https://tauceti.nhost.me/msg/index.php"
OUTPUT_DIR = "found_files"
START_KEY = 1
MAX_RETRIES = 3   # Maximum retry attempts on error

# Create output directory
os.makedirs(OUTPUT_DIR, exist_ok=True)

# List of realistic User-Agents for rotation
USER_AGENTS = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
    'Mozilla/5.0 (iPad; CPU OS 17_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0',
]

# Additional realistic headers
ACCEPT_LANGUAGES = [
    'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
    'ru,en;q=0.9,en-US;q=0.8',
    'en-US,en;q=0.9,ru;q=0.8',
    'ru-RU,ru;q=0.9,en;q=0.8',
]

# Phrases indicating invalid key
INVALID_PHRASES = [
    "не подходит",
    "ключа не существует",
    "ключ не найден",
    "invalid key",
    "key not found"
]

# Phrases indicating ban or server errors
BAN_PHRASES = [
    "521: Web server is down",
    "cloudflare",
    "error code 521",
    "web server is down",
    "доступ ограничен",
    "429 Too Many Requests",
    "try again in a few minutes",
    "access denied",
    "blocked",
    "security check"
]

def get_random_headers():
    """Generate random realistic headers for each request"""
    return {
        'User-Agent': random.choice(USER_AGENTS),
        'Accept-Language': random.choice(ACCEPT_LANGUAGES),
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Cache-Control': 'max-age=0',
        'DNT': '1' if random.random() > 0.5 else None,  # Randomly add Do Not Track
    }

def get_filename_from_response(response, key):
    """Extract filename from Content-Disposition header or URL"""
    # Try to get filename from Content-Disposition
    content_disposition = response.headers.get('Content-Disposition')
    if content_disposition:
        # Extract filename from header
        filename_match = re.search(r'filename\*?=([^;]+)', content_disposition)
        if filename_match:
            filename = filename_match.group(1).strip('"\'')
            # Handle encoded filenames
            if filename.startswith("UTF-8''"):
                filename = unquote(filename[7:])
            return filename

    # Try to get filename from URL
    parsed_url = urlparse(response.url)
    url_filename = os.path.basename(parsed_url.path)
    if url_filename and url_filename != 'index.php':
        return url_filename

    # Generate filename with timestamp
    return None

def is_ban_response(text):
    """Check if response indicates ban or server error"""
    text_lower = text.lower()
    return any(phrase.lower() in text_lower for phrase in BAN_PHRASES)

def is_invalid_response(text):
    """Check if response indicates invalid key"""
    text_lower = text.lower()
    return any(phrase in text_lower for phrase in INVALID_PHRASES)

def save_content(content, response, key):
    """Save found content to file with original name when possible"""
    content_type = response.headers.get('Content-Type', 'application/octet-stream').split(';')[0].strip()

    # Try to get original filename
    filename = get_filename_from_response(response, key)

    if filename:
        # Sanitize filename
        filename = re.sub(r'[<>:"/\\|?*]', '_', filename)
        # Add key prefix to avoid collisions
        filename = f"key_{key}_{filename}"
    else:
        # Generate filename with timestamp - always use .bin for everything
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"key_{key}_file_{timestamp}.bin"

    filepath = os.path.join(OUTPUT_DIR, filename)

    # Handle duplicate filenames
    counter = 1
    while os.path.exists(filepath):
        name, ext = os.path.splitext(filename)
        filepath = os.path.join(OUTPUT_DIR, f"{name}_{counter}{ext}")
        counter += 1

    # Write content
    if isinstance(content, str):
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
    else:
        with open(filepath, 'wb') as f:
            f.write(content)

    return filepath, filename

def make_request(key, retry_count=0):
    """Make HTTP request with error handling and rotating user agents"""
    try:
        session = requests.Session()
        
        # Get random headers for this request
        headers = get_random_headers()
        
        # Add random delay between requests (0.5 to 2.5 seconds)
        if key > START_KEY:  # Don't delay on first request
            delay = random.uniform(0.5, 2.5)
            import time
            time.sleep(delay)
        
        response = session.get(
            f"{URL}?key=THERE_IS_NOTHING_IN_EXISTENCE",
            timeout=15,
            headers=headers,
            stream=True
        )

        # Check for ban
        if response.status_code in [429, 403, 503, 521] or is_ban_response(response.text):
            logging.warning(f"Ban or server error detected for key {key} {response.status_code}")
            return None, True

        return response, False

    except requests.exceptions.RequestException as e:
        logging.error(f"Network error for key {key}: {e}")
        if retry_count < MAX_RETRIES:
            # Exponential backoff on retry
            wait_time = (2 ** retry_count) + random.uniform(0, 1)
            import time
            time.sleep(wait_time)
            return make_request(key, retry_count + 1)
        return None, False

# Main loop
key = START_KEY
ban_detected = False
consecutive_failures = 0
MAX_CONSECUTIVE_FAILURES = 10

while True:
    try:
        # If banned, immediately retry the SAME key with longer delay
        if ban_detected:
            logging.warning(f"Ban detected! Waiting longer before retrying key {key}...")
            import time
            time.sleep(random.uniform(5, 10))  # Longer delay after ban
            ban_detected = False
            consecutive_failures += 1
            
            if consecutive_failures >= MAX_CONSECUTIVE_FAILURES:
                logging.error(f"Too many consecutive failures ({consecutive_failures}). Stopping.")
                break
            continue

        response, is_banned = make_request(key)

        if is_banned:
            ban_detected = True
            continue

        if response is None:
            consecutive_failures += 1
            if consecutive_failures >= MAX_CONSECUTIVE_FAILURES:
                logging.error(f"Too many consecutive failures ({consecutive_failures}). Stopping.")
                break
            continue

        # Reset consecutive failures on successful request
        consecutive_failures = 0

        content_type = response.headers.get('Content-Type', '').lower()

        # Process response
        if 'text/html' in content_type:
            content = response.text

            if is_ban_response(content):
                logging.warning(f"Cloudflare error for key {key}")
                ban_detected = True
            elif not is_invalid_response(content):
                filepath, filename = save_content(content, response, key)
                logging.info(f"KEY {key}: HTML content found! Saved to {filename}")
                logging.info(f"   Preview: {content[:200]}...")
                key += 1
            else:
                logging.info(f"Key {key}: invalid")
                key += 1

        elif 'text/plain' in content_type:
            content = response.text
            filepath, filename = save_content(content, response, key)
            logging.info(f"KEY {key}: Text content found! Saved to {filename}")
            logging.info(f"   Preview: {content[:200]}...")
            key += 1

        else:
            # Binary content or other types
            content = response.content
            filepath, filename = save_content(content, response, key)
            logging.info(f"KEY {key}: File found! Saved to {filename} (Size: {len(content)} bytes)")
            key += 1

    except KeyboardInterrupt:
        logging.info("\nProgram stopped by user")
        break
    except Exception as e:
        logging.error(f"Unexpected error: {e}")
        consecutive_failures += 1
        if consecutive_failures >= MAX_CONSECUTIVE_FAILURES:
            break
