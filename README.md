<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# BlogAPILaravel

BlogAPILaravel est une API de blog fait en laravel qui a pour fonctionalité l'authentification, la verification par email, modification de profile, CRUD de categorie, CRUD de articles, CRUD de commentaires. Et tous ces fonctionnalités ne seront possible qu'apres t'etre connecté.



<p align="center"><a href="#" target="_blank"><img src="https://volkeno.com/images/logo.svg" width="100" alt="Laravel Logo"/></a></p>



## Tech Stack

**API:**  [![My Skills](https://skillicons.dev/icons?i=laravel&&theme=light)](https://skillicons.dev)


## deployment

**cpanel:**  https://blogapilaravel.euleukcommunication.sn/api/article


## Installation

- cloner le project avec :

```bash
  git clone https://github.com/yerrymina1195/blogapilaravel
```
- apres avoir ouvert le projet creer un fichier .env et y copier tout ce qui se trouve dans le fichier .env.exemple car il y'a la clé jwt et les identifiants pour le mail dans ce fichier  .

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
 php artisan migrate:fresh
```
- lancer le projet avec
```bash
php artisan serve 
```
## Les Routes 
- Authentification : prefix-> api/auth
```bash

    Route::post('/register', pour l'inscription avec name, email, password, password_confirmation);
    Route::post('/login', pour la connection avec email, password);
    Route::post('/logout', pour la deconnexion);
    Route::post('/refresh', pour le refresh token);  
    Route::post('/updateProfilUser', pour la modification du profile);     
```

- Article : prefix-> api/category
```bash
   Route::get('/', pour voir tous les categories disponibles);
   Route::get('show/{id}', pour voir une categorie specifique en mettant l'id du categorie comme parametre);
   Route::post('store', pour creer une categorie avec comme champ title nb: auth necessaire );
   Route::delete('delete_category/{id}', en mettant l'id du categorie nb: auth necessaire );
   Route::put('update_category/{id}', en mettant l'id du categorie et comme champ title nb: auth necessaire   );       
```

- Article : prefix-> api/article
```bash
   Route::get('/', pour voir tous les articles disponibles);
   Route::get('show/{id}', pour voir un article specifique en mettant l'id du article comme parametre);
   Route::post('store', pour creer un article avec comme champ name, content, category_Id, image(nullabe) nb: auth necessaire );
   Route::delete('delete_article/{id}', en mettant l'id de l'article nb: auth necessaire );
   Route::put('update_article/{id}', en mettant l'id de l'article et comme champ name, content, category_Id, image(nullabe) nb: auth necessaire   );      
```

- Article : prefix-> api/comment
```bash
   Route::post('store',pour creer un commentaire avec comme  content, article_Id,  nb: auth necessaire );
   Route::put('update_comment/{id}',mettre à jour un commentaire  en mettant l'id du commentaire nb: auth necessaire);
   Route::delete('delete_comment/{id}', supression en mettant l'id du commentaire  nb: auth necessaire );
```

