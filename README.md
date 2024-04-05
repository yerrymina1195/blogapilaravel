<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# BlogAPILaravel

BlogAPILaravel est une API de blog fait en laravel qui a pour fonctionalité l'authentification, la verification par email, modification de profile, CRUD de categorie, CRUD de articles, CRUD de commentaires. Et tous ces fonctionnalités ne seront possible qu'apres t'etre connecté.



<p align="center"><a href="#" target="_blank"><img src="https://volkeno.com/images/logo.svg" width="100" alt="Laravel Logo"/></a></p>



## Tech Stack

**Client:**  [![My Skills](https://skillicons.dev/icons?i=laravel&&theme=light)](https://skillicons.dev)





## Installation

- cloner le project avec :

```bash
  git clone https://github.com/yerrymina1195/blogapilaravel
```
- apres avoir ouvert le projet creer un fichier .env et y copier tout ce qui se trouve dans le fichier .env.exemple .

- installer les dépendances avec composer en Exécutant sur votre cmd ou terminal : 
```bash
composer install
```
- installer les dépendances npm :
```bash
npm install
```
- executer la commande;
```bash
npm run build
```
- générer les clés :
```bash
php artisan key:generate
```
- migration et seed de la base de données
```bash
 php artisan migrate:fresh --seed 
```
- lancer le projet avec
```bash
php artisan serve 
```
## Les Routes 
- Authentification :
```bash
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);  
    Route::post('/updateProfilUser', [AuthController::class, 'updateUserProfil']);     
});
```





