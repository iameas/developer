# WEB AUTHENTICATION

This is a simple web authentication system featuring user login and signup. users can sign up with email verification, upload a profile picture, and navigate the system based on Role-Based Access (RBAC), the default access is "customer". The project is built using **PHP, HTML, Bootstrap and Javascript**

## Features

- User login and signup
- Email verification via a code before account activation
- Profile picture upload during signup
- Role-Based Access Control (RBAC) for user roles (Admin, Customer, Developer, etc.)

## Installation

1. **Clone the repository**
   `git clone https://github.com/iameas/developer.git`
   
`cd developer`

3. **Setup XAMPP**

- Start **Apache** and **MySQL** in XAMPP
- Open **phpmyadmin** (`http://localhost/phpmyadmin/`)
- Import the database from project directory

3. **Database Setup**

- Import the SQL file into phpMyAdmin to create tables and test users
- The system includes a test user

```
    Username: iameas
    Password: TheProgrammer.1
```

4. **Run the Project**

- Place the project folder in the `htdocs` directory (`C:/xampp/htdocs/developer`)
- Open a browser and visit:

```
    http://localhost/developer/
```

## Usage

- **Signup** visit: (`http://localhost/developer/wws-register.php`) to register. Enter your details, request for a verification code, verify email, and complete the signup process.
- **Login** visit: (`http://localhost/developer/wws.php`) to login

## Technology Used

- PHP,
- HTML,
- Bootstrap, and
- Javascript.

## License

This project is open source. Feel free to modify and improve it.

---

Enjoy using the authentication system!
