# ConceptNet-based Project

> A website that features a variety of ways to use the ConceptNet API. Users can consult various tables and play two
> different games about the subject of their choice!

![Home page](/og-image.png)

## ⚡ Technologies Used

- **Frontend:** Javascript, CSS
- **Backend:** PHP, MySQL, Python, HTML
- **API:** ConceptNet
- **Frameworks & Libraries:** jQuery, Bootstrap, Sammy.js, Mustache, DataTables

## 🛠 Set Up

#### Prerequisites:

- PHP
- MySQL
- Python

#### Once the project is downloaded, do the following:

1. Set up your MySQL database information (`DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`) inside the
   file `create-db.py`. Do the same inside  `server/config/database.php`
2. Inside `create-db.py`, update the main function to insert a user into the database with your preferred credentials
3. Create a MySQL database by running `python create-db.py`
4. Launch the project on localhost with `php -S localhost:8888`

Moreover, a backup HTML table containing ConceptNet information can be created by running `python seed.py`

## 📩 Contact

For questions or feedback, reach out at:

- **Email:** alexandre.stang.web@gmail.com
- **LinkedIn:** [/alexandre-stang](https://www.linkedin.com/in/alexandre-stang-163208a7/)
