# Dummy goods parser for WB
This parser can be used with cron jobs. After finding cheap product or product with big discount, bot will send message to your channel. You need to add bot to your channel with rights to posting messages.
## Usage: 
before execution add your telegram bot token and channel id into common/config/params.php. Also, you can change settings in same file.
To start parsing use command in terminal from project directory:
```
php yii file/parse-catalog [catalogname]
```
## Result example:
```
https://www.wildberries.ru/catalog/110xx3095/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/61x63596/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/64xx1236/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/55x92945/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/551x2946/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/551x2949/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/90x4712/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/12x59674/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/88x918/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/264x308/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/62xxx465/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/129xxx674/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/11xxx3095/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/129xxx4/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/904xxx2/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/1295xxx4/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/891xx75/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/1295x674/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/1120xx911/detail.aspx?targetUrl=GP
https://www.wildberries.ru/catalog/11204xxx0/detail.aspx?targetUrl=GP
```
