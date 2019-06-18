# Liveclicker users CRUD
## API Configuration
You have to execute the nexts instructions after clone the project. Make sure you have configured de .env file with a valid database.

1. ```composer install```

2. ```composer update```

3. ```php artisan migrate```

4. ```php artisan db:seed```

5. ```php artisan passport:install```

6. ```rm public/storage``` and ```php artisan storage:link```

7. ```sudo chmod -R 777 storage``` (Only on linux systems)

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

## API Docs

You can use the api without the clients. For this i recomend use postman.

The api manipulate two types of users (user, admin), both have an endpoint to login and get the token for use on protected routes. After login put the Bearer token with the Authorizathion header for all request on protected endpoints.

- For example, to login you have to do a post petition with email and password on ```http://{local-host}/api/admin/login```

- Retrieve all users: Do a get request on ```http://{local-host}/api/admin/login``` with the Authorization header. Remember, the token its the same that you get when you do the post login.

- POST ```http://{local-host}/api/admin/users```, params: { name, surname, email, password, c_password, profile_image(jpg) }, create a new user.

- POST ```http://{local-host}/api/admin/users/{id}```, params: { 'Whatever you want to update' } (The post method is needed because the file upload), update the user data.

- DELETE ```http://{local-host}/api/admin/users/{id}```, delete user and related media.

- PUT ```http://{local-host}/api/admin/users/{id}```, params : { active(1 or 0) }, toggle active attribute.

Finally, the Admin credentials are
- Email: support@liveclicker.com
- Password: zapallo2019
