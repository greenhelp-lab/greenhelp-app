# 🌿 GreenHelp — Green IT Platform

A web platform project designed to simulate and support the adoption of Green IT practices inside tech business through a company that offers and implements a variety of services - Greenhelp

This project explores how sustainability services could be structured, tracked, and visualized through a digital product — combining environmental awareness with product thinking.

---

## 📌 Project Overview

GreenHelp is a full-stack web application where companies can:

- Explore sustainability-focused IT services  
- Simulate contracting those services  
- Track progress and environmental impact  
- Visualize their “green maturity” through a scoring system  

The project focuses on turning abstract sustainability initiatives into something structured, measurable, and interactive.

---

## 🎯 Purpose of the Project

This project was built to explore:

- Product design for sustainability-focused platforms  
- Service catalog + lifecycle tracking systems  
- Gamification through scoring (Green Score)  
- Role-based systems (client vs admin)  
- Full-stack development with a simple, structured architecture  

> This is not a production-ready system, but a functional prototype demonstrating how a Green IT platform could work in practice.

---

## ⚙️ Core Features

### Authentication & Roles
- Login/logout system  
- Role-based access (client, support, admin)  
- Session validation for protected routes  

### Service Catalog
- List of sustainability services (cards)  
- Each service includes:
  - Description  
  - Sustainability category  
  - Green score value  
- Filtering by category  
- Real-time search  

### Cart & Service Simulation
- Add/remove services  
- Simulated acquisition flow  
- Aggregated sustainability score preview  

### Service Tracking
- View contracted services  
- Status tracking:
  - Pending  
  - In progress  
  - Completed  
  - Cancelled  

### Green Scoring System
- Each service contributes to a total score  
- Score is distributed across sustainability areas  
- Provides a simple “maturity overview” of the company  

### Admin Panel
- Manage users and companies  
- Manage service catalog  
- View platform-level metrics and scores  

---

## 🧱 Tech Stack

- **Backend:** PHP  
- **Frontend:** HTML, CSS, JavaScript  
- **Database:** MySQL  

### Structure Highlights
- Separation of concerns (logic, UI, config)  
- Semantic commits *(in progress / intended)*  

---

## 🗃️ Data Model (Simplified)

Main entities:

- `usuarios` — authentication, roles, status  
- `empresas` — company data  
- `areas_sustentaveis` — sustainability categories  
- `servicos` — available services  
- `carrinho` — temporary selections  
- `servicos_andamento` — acquired services + status  
- `pontuacoes_sustentaveis` — scoring per company  

---

## 🔄 User Flow

### Client
1. Logs into the platform  
2. Views company overview  
3. Explores service catalog  
4. Adds services to cart  
5. Confirms acquisition (simulation)  
6. Tracks progress and sustainability score  

### Admin
1. Logs in with admin role  
2. Manages users and companies  
3. Updates service catalog  
4. Monitors platform metrics  

---

## 🎨 Design Direction

- Clean and accessible UI  
- Sustainability-inspired visual language  
- Focus on clarity and usability over complexity  

> *(Screenshots and Figma prototype will be added soon)*

---

## 🚧 Project Status

This project is currently:

- Partially implemented  
- Not fully refactored  

Some features and structures are still rough and will be improved over time.

---

## ▶️ Running Locally

```bash
git clone https://github.com/greenhelp-lab/greenhelp-app.git
cd greenhelp-app
