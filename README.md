# A reconstructured Alarin apricot storage system
Alarin system software was used initially to store information about apricots stored in warehouses of A&A. I suspect that later system was corrupted or captured by searchers. They modified the system to store files and procedures available by keys. The purpose of the modified system is unknown. 

This reconstructured version based on original RCS commits by mr. Alabaster. Current version are based on `Alarin` (action system) and later improvements of `Wiper` (file storage and dynamic keys).

## Preview
| ![Capricot Main Page](<img/Capricot - Main.png>) | ![Alarin Main Page](<img/Alarin - Main.png>) |
|--------------------------------------------------|-----------------------------------------------|

## Apricot types
Here's the list of apricot types available for storing in system. These apricot types are was found in first commit of original system as a telefax scan:

- Carmingo
- Ben Dor
- Maturity Chart
- Wonder Cot
- Sunny Cot
- Solar Sweet
- Solar Nugget
- Solar Gem
- Pisana
- Perle Cot
- Orange Red
- Red Blush
- Hula Blush
- Dawn
- Paz
- Lilly Cot
- Earli Sun

## History
Initially command to control number apricots was `apricot`. Later on this command was shorten to `ap`. So, after `Capricorn` version (G8) prefer use `ap` to see number of apricots stored. To increase number of apricots use `ap add` command. To decrease, use `ap sub` command. Notice: you can not store more than `apricot_max` apricots. This number of apricots should be set in configuration file, to constraint that there is enough space on warehouse to store all apricot fruits. 

> [!NOTE]
> Original projet uses different technologies and styles for main page and sources. I left the source code only for `original.html` template. While restoring project, several methods where refactored to constraint modern rules of creating web application. In fact, the security problem with RPC procedures was solved. Now it is should be unavailable to patch source code via client side which was available in time period from `Forwax` to `Mensaje` versions of project. 

I also remain the ability to create your own hidden files and procedures to call, but only a system administrator can do it. Administrator of system can create a custom actions to access for files or execute procedures. All this actions available by entering special key to the system.

## Usage
To use system, just create your own copy of it and change the values in `config.php` script. Define files, stored by keys in `$keys` array. Create remote. procedures available for calling via `$dynamic_keys` array. You also may extend the system abilities by simply extending the `index.php` script with your own cascades of check on entered keys. In `Wiper` version was implemented support for AJAX queries, available via usage of `X-Requested-With` with value of `XMLHttpRequest` so you also may work with system continuously. Also in `GarFax` was implemented the availability too see graph of all keys via graph key. Use `$meta` array to add additional vertexes and edges to graph of system keys.

You may see all useful templates in `/examples` folder.

To run the system, simply create an php server of it with this command:

```zsh
php -S localhost:8080
```

To access system via browser, fill the `$template` variable with path of created html template for system. Then open the `localhost:8080` (or your custom path) in web browser.

You also can use system via CLI tools without a GUI. To do this you need only to pass the `X-Requested-With` header with value of `XMLHttpRequest`. Use this command as a snippet to do this:

```zsh
curl \
  -H "X-Requested-With: XMLHttpRequest" \
  "http://localhost:8080/index.php?key=KEY"
```
