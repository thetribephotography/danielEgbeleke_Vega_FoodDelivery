<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

Good Day, Kindly Find Below A Breakdown Of How To Setup the System On Your machine and Have It Up And Running.

REQUIREMENTS:
- PHP (VERSION 8.2)
- Composer Has To be Installed On Your System. You can Download It Here and Install: https://getcomposer.org


PROJECT SET-UP:
- Pull from the Directory
- Create an Env File using a draft from the 'env.example' file, Copy the Details Below Into your newly created ENV
- Navigate to the Codebase directory in your cmd Run 'composer i'  to install all dependencies
- Run 'php artisan key:generate'
- Run 'php artisan migrate' 
- Run 'php artisan db:seed'
- Run 'php artisan optimize'...If this command runs successfully you should have no issues with the system running
- To Finally start the Application Open 3 cmd Iterations all in the codebase directory and run these 3 commands(1 in Each) and leave these cmd's on and running throughout your testing Time
     - php artisan serve --port=9440
     - php artisan schedule:work
     - php artisan queue:work

Postman Documentation: https://documenter.getpostman.com/view/36671417/2sAYdhLWDs

ENV DETAILS TO BE COPIED:
DB_CONNECTION=sqlite
DB_DATABASE=/database.sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
BREVO_URL=https://api.brevo.com/v3/smtp/email
BREVO_API_KEY=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


Happy Testing🤗.
