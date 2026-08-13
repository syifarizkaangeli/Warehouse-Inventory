# 📦 Warehouse Inventory System

A simple web-based inventory management system built with PHP and MySQL.

This application is designed to help manage warehouse inventory, including product data, stock quantities, prices, inventory value, and printable inventory reports.

---

## ✨ Features

- 📊 Inventory dashboard
- 📦 Product inventory management
- ➕ Add products
- ✏️ Edit products
- 🗑️ Delete products
- 🔎 Search inventory
- 📉 Low-stock detection
- 💰 Automatic inventory value calculation
- 📋 Inventory reports
- 🖨️ Print reports
- 📱 Responsive mobile interface
- 🖥️ Desktop-friendly dashboard

---

## 🛠️ Technologies

- PHP
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript
- Bootstrap CDN

---

## 📁 Project Structure

```text
Warehouse-inventory/
│
├── config.php
├── index.php
├── beranda.php
├── laporan.php
├── style.css
├── gudang.sql
├── README.md
└── .gitignore

## 🚀 How to Run
1. Install XAMPP

Make sure Apache and MySQL are installed.
Start:
Apache
MySQL

2. Move the Project

Put the project inside:
C:\xampp\htdocs\

The final location should be:
C:\xampp\htdocs\Warehouse-inventory

3. Setup Database

Open:
http://localhost/phpmyadmin

Import:
gudang.sql

The SQL file will create the gudang database and barang table.

4. Open the Application

Open:
http://localhost/Warehouse-inventory/beranda.php

## 📊 Main Pages
Dashboard
/beranda.php

Displays:
Total product types
Total stock
Total inventory value
Low-stock products
Recently added products
Inventory
/index.php

Used to:
Add products
Edit products
Delete products
Search products
View stock
View inventory value
Reports
/laporan.php

Displays a complete inventory report and provides a print function.

## 🗄️ Database

Database:
gudang

Main table:
barang

Columns:

Column	Description
id	Product ID
nama_barang	Product name
jumlah	Stock quantity
harga_per_pcs	Price per unit
created_at	Creation date
updated_at	Last update date

## 🔐 Security

The application uses PDO prepared statements for database queries to reduce SQL injection risks.
User-provided values are also escaped when displayed in HTML.

## 📱 Responsive Design

The interface supports:
Desktop
Laptop
Tablet
Mobile

The sidebar automatically changes into a mobile navigation drawer on smaller screens.

##📄 License

This project was created for educational and inventory management purposes.

*.log
.DS_Store
Thumbs.db
.idea/
.vscode/
