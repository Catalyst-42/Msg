import requests
import logging

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
    handlers=[
        logging.StreamHandler()
    ]
)

number_of_try = 1
while True:
    result = requests.get(f"https://tauceti.nhost.me/msg/index.php?key={number_of_try}")
    
    logging.info(
        {
            "try": number_of_try,
            "status": {result.status_code},
            "text": result.text[:10] if len(result.text) > 100 else result[:20],
        }
    )

    number_of_try += 1
