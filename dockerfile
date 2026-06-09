FROM php:8.2-apache
COPY . /var/www/html/
# تغيير ملكية المجلد لمستخدم الويب (www-data) وإعطاؤه صلاحية الكتابة
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 775 /var/www/html/
EXPOSE 80
