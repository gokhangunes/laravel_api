# laravel_api

## Teknoloji 
| teknoloji |
| ------ | 
| docker | 
| redis | 
| rabbit |
| mysql | 
| nginx | 
|  php7.4 |
| laravel 7 |

google, ios,  3rd-party mock endpoindleri proje içerisinde oluşturuldu.


queue için rabbitmq kullanıldı

3rd-party istekleri için 3 event ve 3 listener tanımlandı listenirler'den istekler queue(AplicationJob) atılıyor.

Subscription expire_date kontrolü için command oluşturuldu expire date boş olan (canceled) subscriptionlar satır satır queue(SubscriptionCheckJob) aktarılıyor

 docker-compose up -d
 
 cd api_project && composer install
 
 docker-compose exec laravel bash 
 
 php artisan queue:work
 
 php artisan subscription:check

 

