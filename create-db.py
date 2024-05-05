import mysql.connector
from mysql.connector import Error
from bs4 import BeautifulSoup

DB_HOST = 'www-ens.iro.umontreal.ca'
DB_NAME = 'stangale_ift3225_tp2'
DB_USER = 'hello'
DB_PASSWORD = 'hello'


def create_database(connection, query):
    cursor = connection.cursor()
    try:
        cursor.execute(query)
        connection.commit()
        print("Database created successfully")
    except Error as err:
        print(f"Error: {err}")


def create_table(connection, query):
    cursor = connection.cursor()
    try:
        cursor.execute(query)
        connection.commit()
        print("Table created successfully")
    except Error as err:
        print(f"Error: {err}")


def insert_backup_data(connection):
    # Load HTML file and parse data
    with open('./backup/facts_table.html', 'r') as file:
        soup = BeautifulSoup(file, 'html.parser')

    rows = soup.find_all('tr')[1:]  # Skip the header row
    data = [
        (row.find_all('td')[0].text, row.find_all('td')[1].text, row.find_all('td')[2].text, row.find_all('td')[3].text)
        for row in rows]

    cursor = connection.cursor()
    insert_query = """
    INSERT INTO facts (start_id, start, relation, end)
    VALUES (%s, %s, %s, %s);
    """
    try:
        cursor.executemany(insert_query, data)
        connection.commit()
        print("Data inserted successfully")
    except Error as err:
        print(f"Error: {err}")


def insert_user(connection, username, password):
    cursor = connection.cursor()
    insert_query = """
    INSERT INTO users (username, password)
    VALUES (%s, %s);
    """
    try:
        cursor.execute(insert_query, (username, password))
        connection.commit()
        print("User inserted successfully")
    except Error as err:
        print(f"Error inserting user: {err}")


def main():
    # Paramètres de connexion à la base de données
    config = {
        'user': DB_USER,
        'password': DB_PASSWORD,
        'host': DB_HOST
    }

    # Créer une connexion au serveur MySQL
    try:
        conn = mysql.connector.connect(**config)
        if conn.is_connected():
            print('Connected to MySQL server')

            # Créer la base de données
            create_database(conn, f"CREATE DATABASE IF NOT EXISTS {DB_NAME}")

            # Se connecter à la base de données créée
            conn.database = DB_NAME;

            # Create facts table
            table_query = """
            CREATE TABLE IF NOT EXISTS facts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                start_id VARCHAR(255) NOT NULL,
                start VARCHAR(255) NOT NULL,
                relation VARCHAR(100) NOT NULL,
                end VARCHAR(255) NOT NULL
            );
            """
            create_table(conn, table_query)

            # Create users table
            table_query = """
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                score INT DEFAULT 0
            );
            """
            create_table(conn, table_query)

            # Insert the user
            insert_user(conn, 'ift3225', '5223tfi')

            # Insert facts
            insert_backup_data(conn)

    except Error as err:
        print(f"Error: {err}")

    finally:
        if conn is not None and conn.is_connected():
            conn.close()


if __name__ == "__main__":
    main()
