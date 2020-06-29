# API de recettes basé sur des outils puissants et populaires

## Technologies utilisées
* [API Platform](https://api-platform.com)
* [Lexik JWT](https://github.com/lexik/LexikJWTAuthenticationBundle)
* [Mercure](https://mercure.rocks)
* [Souin](https://github.com/darkweak/souin)
* [Vulcain](https://vulcain.rocks)

## Étapes

## Installation de maker-bundle
Premièrement, installons le maker bundle pour gagner du temps en créant des entités de manière automatisée  
```bash
docker-compose exec php composer req symfony/maker-bundle --dev
```

### Mise en place de l'authentification
Grâce à `maker-bundle` nous allons pouvoir créer notre classe très rapidement. Pour créer la classe `User` nous utiliserons `make:user`. Cela aura pour effet de nous créer une classe User qui implémente la UserInterface et nous fait tout le setup de la liaison avec doctrine.

```bash
docker-compose exec php bin/console make:user
```

```bash
The name of the security user class (e.g. User) [User]:
> 

Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
> 

Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
> 

Does this app need to hash/check user passwords? (yes/no) [yes]:
> 
```

Pour la partie authentification, nous utiliserons le bundle `LexikJWTAuthenticationBundle`. Pour l'installer, rien de plus simple:
```bash
docker-compose exec php composer req jwt-auth
```

Puis il suffit de lancer cette commande suivante pour générer les clés:
```bash
docker-compose exec php sh -c '
    set -e
    apk add openssl
    mkdir -p config/jwt
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```

Ensuite vous n'avez qu'à modifier le security.yaml avec l'exemple prêt à l'emploi qui suit:
```yaml
# ...
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            stateless: true
            anonymous: true
            provider: app_user_provider
            json_login:
                check_path: /authentication_token
                username_path: email
                password_path: password
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure
            guard:
                authenticators:
                    - lexik_jwt_authentication.jwt_token_authenticator

    access_control:
        - { path: ^/docs, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/authentication_token, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, roles: IS_AUTHENTICATED_FULLY }
```

Il nous faut aussi définir la route `/authentication_token` dans le `routes.yaml` comme suit:
```yaml
authentication_token:
    path: /authentication_token
    methods: ['POST']
```

### Mise en place de l'API
API Platform est supporté par `maker-bundle` grâce au drapeau `--api-resource` ou `-a`

Créons donc notre entité `Recipe`
```bash
docker-compose exec php bin/console make:entity Recipe -a
```

````bash
New property name (press <return> to stop adding fields):
> name

Field type (enter ? to see all types) [string]:
> 

Field length [255]:
> 1024

Can this field be null in the database (nullable) (yes/no) [no]:
> 

Add another property? Enter the property name (or press <return> to stop adding fields):
> author

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> User

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToOne

Is the Recipe.author property allowed to be null (nullable)? (yes/no) [yes]:
> no

Do you want to add a new property to User so that you can access/update Recipe objects from it - e.g. $user->getRecipes()? (yes/no) [yes]:
> 

New field name inside User [recipes]:
> 

Do you want to automatically delete orphaned App\Entity\Recipe objects (orphanRemoval)? (yes/no) [no]:
> yes

Add another property? Enter the property name (or press <return> to stop adding fields):
> 
````

Merci au `maker-bundle` pour gérer aussi les relations entre entités et nous faire gagner énormément de temps.  
Nous n'avons pas d'autres champs relatifs à cette entité à renseigner pour le moment, nous pouvons donc passer à l'entité `Ingredient`

```bash
docker-compose exec php bin/console make:entity Ingredient -a
```

````bash
New property name (press <return> to stop adding fields):
> name

Field type (enter ? to see all types) [string]:
> 

Field length [255]:
> 1024

Can this field be null in the database (nullable) (yes/no) [no]:
>

Add another property? Enter the property name (or press <return> to stop adding fields):
> 
````

Nous voulons maintenant créer la relation entre une recette et des ingredients avec des quantités. Nous devons aussi créer une table pour gérer les unités de mesures (Kg, L, cuillère à soupe, cuillère à café, etc...)

Créons d'abord la table `Unit` qui contiendra juste un nom pour l'unité

```bash
docker-compose exec php bin/console make:entity Unit -a
```

````bash
New property name (press <return> to stop adding fields):
> name

Field type (enter ? to see all types) [string]:
> 

Field length [255]:
>

Can this field be null in the database (nullable) (yes/no) [no]:
>

Add another property? Enter the property name (or press <return> to stop adding fields):
> 
````

Ensuite créons la table `RecipeIngredient` pour gérer la relation entre une recette et un ingrédient. Nous ajouterons aussi le lien avec une unité et ajouterons une quantité relative au combo de recette-ingrédient

```bash
docker-compose exec php bin/console make:entity RecipeIngredient -a
```

```bash
New property name (press <return> to stop adding fields):
> quantity

Field type (enter ? to see all types) [string]:
> float

Can this field be null in the database (nullable) (yes/no) [no]:
> 

Add another property? Enter the property name (or press <return> to stop adding fields):
> unit

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> Unit

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToOne

Is the RecipeIngredient.unit property allowed to be null (nullable)? (yes/no) [yes]:
> no

Do you want to add a new property to Unit so that you can access/update RecipeIngredient objects from it - e.g. $unit->getRecipeIngredients()? (yes/no) [yes]:
> 

New field name inside Unit [recipeIngredients]:
> 

Do you want to automatically delete orphaned App\Entity\RecipeIngredient objects (orphanRemoval)? (yes/no) [no]:
> yes

Add another property? Enter the property name (or press <return> to stop adding fields):
> recipe

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> Recipe

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToOne

Is the RecipeIngredient.recipe property allowed to be null (nullable)? (yes/no) [yes]:
> no

Do you want to add a new property to Recipe so that you can access/update RecipeIngredient objects from it - e.g. $recipe->getRecipeIngredients()? (yes/no) [yes]:
> 

New field name inside Recipe [recipeIngredients]:
> 

Do you want to automatically delete orphaned App\Entity\RecipeIngredient objects (orphanRemoval)? (yes/no) [no]:
> yes

Add another property? Enter the property name (or press <return> to stop adding fields):
> ingredient

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> Ingredient

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToOne

Is the RecipeIngredient.ingredient property allowed to be null (nullable)? (yes/no) [yes]:
> no

Do you want to add a new property to Ingredient so that you can access/update RecipeIngredient objects from it - e.g. $ingredient->getRecipeIngredients()? (yes/no) [yes]:
>    

New field name inside Ingredient [recipeIngredients]:
> 

Do you want to automatically delete orphaned App\Entity\RecipeIngredient objects (orphanRemoval)? (yes/no) [no]:
> yes

Add another property? Enter the property name (or press <return> to stop adding fields):
> 
```

Passons maintenant à l'entité `Step` puisqu'une recette contient des étapes. Une étape est référencée dans 1 recette et référence 0 à plusieurs ingrédients puisqu'une étape peut être de simplement mélanger énergiquement par exemple.

```bash
docker-compose exec php bin/console make:entity Step -a
```

```bash
New property name (press <return> to stop adding fields):
> action

Field type (enter ? to see all types) [string]:
> 

Field length [255]:
> 1024

Can this field be null in the database (nullable) (yes/no) [no]:
> 

Add another property? Enter the property name (or press <return> to stop adding fields):
> recipe

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> Recipe

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToOne

Is the Step.recipe property allowed to be null (nullable)? (yes/no) [yes]:
> no

Do you want to add a new property to Recipe so that you can access/update Step objects from it - e.g. $recipe->getSteps()? (yes/no) [yes]:
>

New field name inside Recipe [steps]:
> 

Do you want to automatically delete orphaned App\Entity\Step objects (orphanRemoval)? (yes/no) [no]:
> yes

Add another property? Enter the property name (or press <return> to stop adding fields):
> ingredients

Field type (enter ? to see all types) [string]:
> relation

What class should this entity be related to?:
> Ingredient

Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
> ManyToMany

Do you want to add a new property to Ingredient so that you can access/update Step objects from it - e.g. $ingredient->getSteps()? (yes/no) [yes]:
> 

New field name inside Ingredient [steps]:
> 

Add another property? Enter the property name (or press <return> to stop adding fields):
> 
```

### Définition des points d'API
Pour définir nos points d'API, nous profiterons de l'annotation `APIResource`. Nous pourrons restreindre des méthodes HTTP et donc des actions sur l'API à certaines catégories utilisateurs.  
Par exemple, nous voulons qu'un utilisateur authentifié puisse créer une recette mais qu'il soit le seul à pouvoir la modifier, nous allons mettre en place cette annotation.  
Malheureusement, `maker-bundle` ne nous permet pas de modifier les entités à postériori
