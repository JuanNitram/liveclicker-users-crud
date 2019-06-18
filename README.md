# Liveclicker users CRUD
## API Configuration
You have to execute the nexts instructions after clone the project. Make sure you have configured de .env file with a valid database.

1. ```composer install```

2. ```composer update```

3. ```php artisan migrate```

4. ```php artisan db:seed```

5. ```php artisan passport:install```

6. ```sudo chmod -R 777 storage```

## Page - Client

1. ```npm install```

2. ```npm run build```

3. Go to the nuxt.config.js file and change the apiUrl and change for your localhost API url.
```http://{local-host}/public/api/page/``` or ```http://{local-host}/api/page/``` if you have a virtual host.

4. ```npm run dev``` or ```npm start```

5. Enjoy the App on ```http://localhost:4000```

## Admin - Client

1. ```npm install```

2. ```npm run build```

3. Go to the nuxt.config.js file and change the apiUrl and change for your localhost API url.
```http://{local-host}/public/api/admin/``` or ```http://{local-host}/api/admin/``` if you have a virtual host.

4. ```npm run dev``` or ```npm start```

5. Enjoy the App ```http://localhost:3000```

##API Docs

You can use the api without the clients. For this i recomend use postman.

The api manipulate two types of users (user, admin), both have an endpoint to login and get the token for use on protected routes. Before login put the Bearer token con the Authorizathion header for all request protected.
