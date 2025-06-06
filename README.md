# 🏆 Tournament App Backend

Welcome to the backend of the Tournament App — a robust and scalable solution to manage tournaments effectively. This backend handles user registration, tournament creation, match scheduling, notifications, and much more, providing a seamless experience for both organizers and participants.

---

## 🚀 Features

- 🔐 **User Management** – Register, authenticate, and manage user profiles.
- 🏟️ **Tournament Creation** – Create and configure new tournaments.
- 📅 **Match Scheduling** – Automate game scheduling and bracket generation.
- 🔔 **Notifications** – Send real-time SMS updates using Fast2SMS.
- 💳 **Payment Integration** – Secure tournament fee processing.
- 🛠️ **Admin Panel** – Dashboard to manage tournaments and users.

---

## 🛠️ Tech Stack

- **Language**: PHP
- **Database**: MySQL
- **API Type**: RESTful APIs
- **Third-party Integration**: Fast2SMS (for notifications)

---

## 📁 Project Structure

Tournament-App-Backend/

├── AdminPanel/ # Admin dashboard files

├── GamesImages/ # Game-related media

├── Payment-Gateways/ # Payment integration logic

├── ProfilePics/ # User profile images

├── TournamentImages/ # Tournament images

├── data-functions.php # Data manipulation and queries

├── dbcon.php # DB connection setup

├── fast2sms_api.txt # Fast2SMS API key file

├── games.php # Game logic endpoints

├── global-functions.php # Common helper functions

├── in-app-actions.php # Core app functionalities

├── index.php # App entry point

├── main-data.php # Tournament data logic

├── notification.php # Send notifications

├── notifications.php # Manage notification templates

├── transactions.php # Payment transaction logic

├── users.php # User-related APIs

└── README.md # Documentation


---

## ⚙️ Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/PATELMIHIR2715/Tournament-App-Backend.git
   cd Tournament-App-Backend
   ```
2. **Database Configuration**

   - Create a MySQL database.

   - Import the schema (SQL dump if provided).

   - Edit dbcon.php with your DB credentials.

3. **Fast2SMS Setup**

   - Get your API key from Fast2SMS.

   - Replace the value in fast2sms_api.txt.
4. **Deploy to Local Server**

   - Copy project to your web server (e.g. htdocs for XAMPP).

   - Access via http://localhost/Tournament-App-Backend.


## 📬 API Endpoints (Sample)

| Endpoint               | Method | Description                      |
|------------------------|--------|----------------------------------|
| `/users.php`           | GET    | Fetch user data                  |
| `/games.php`           | GET    | List games                       |
| `/transactions.php`    | POST   | Handle transactions              |
| `/notification.php`    | POST   | Send SMS to participants         |
| `/in-app-actions.php`  | POST   | Execute app operations           |

> ⚠️ Ensure to implement validation and authentication for secure access.

## 🤝 Contributing

Contributions are welcome! Feel free to:

- Fork the repository  
- Create a feature branch  
- Submit a pull request  

For major changes, please open an issue first to discuss your ideas.

---



## 📞 Contact

**Developer**: Mihir Patel  
**GitHub**: [PATELMIHIR2715](https://github.com/PATELMIHIR2715)  
**Email**: [patelmihir2712005@gmail.com](mailto:patelmihir2712005@gmail.com)











