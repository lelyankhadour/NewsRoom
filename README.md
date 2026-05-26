# 🚀 NewsRoom

## 📝 Introduction
**NewsRoom** A robust, scalable, and modular content management system built with Laravel. This project strictly follows the Clean Architecture paradigm to ensure separation of concerns, maintainability, and high-performance throughput.The system allows writers to compose and publish content and ensures seamless delivery of system notifications via multiple channels. 

---

## 📚 Table of Contents
1. [Project Overview](#project-overview)  
2. [Tech Stack](#tech-stack)  
3. [Core Features](#core-features)  
4. [Architectural Philosophy & System Design](#Architectural-Philosophy-&-System-Design)  
5. [Installation](#installation)  
6. [API Endpoints](#api-endpoints)  
7. [API Documentation](#API-Documentation)  



---

## 📌 Project Overview
NewsRoom is a backend API designed for professional news and content publishing, where:
- Writers compose and manage their own content.
- Administrators monitor platform and system notifications .
- Asynchronous processing handles heavy tasks like notification dispatching and report generation.

---

## ⚙️ Tech Stack

- **Framework:** Laravel  
- **Language:** PHP 8.3  
- **Database:** MySQL  
- **Auth:** Laravel Sanctum 
- **Queue/Async:** Redis / Database 
- **Architecture:** Service Layer + Interfaces + Clean Controllers  
- **API Format:** JSON Only  
---
## ✨ Core Features
- **Article Lifecycle Management:** Create, update, and publish articles with status tracking.
- **Dynamic Tagging System:** Robust synchronization for article categorization.
- **Context-Aware Notifications:** Automated, role-based alerts (Admin vs. Writer).
- **Asynchronous Processing:** Background task execution for notifications and reports using Queues.
- **Reporting Engine:** Strategy-based generation of weekly and monthly platform analytics.
- **Automated Reporting Engine:** Strategy-based generation of platform analytics (weekly/monthly) accessible via CLI commands or system events.
---

## 🏗️ Architectural Philosophy & System Design
This system is engineered based on **SOLID principles** and advanced **Design Patterns**, creating a modular ecosystem where each component has a single, well-defined responsibility.

### The Synergy of Design Patterns
Our architecture functions as a unified pipeline:

* **The Data Layer:** We use the **Repository Pattern** to abstract data access, layered with the **Decorator Pattern** to inject a Caching layer. By resolving these as **Singletons** in the Service Container, we ensure memory efficiency and consistent data state.
* **The Domain Layer:** Business logic resides in the **Service Layer**, which interacts only with **Interfaces (Contracts)**. This ensures **Dependency Inversion**, making our core logic completely **Decoupled** from database implementations.
* **Extensibility & Logic:** For dynamic tasks like Reporting, we utilize the **Strategy Pattern**, allowing the system to be **Open/Closed**—new report types can be added without modifying the core `ReportService`.
* **Reactive Flow:** We employ an **Event-Driven Architecture** to handle side effects (like notifications). When an article is published, the system fires an event, triggering independent listeners.
 <!-- * **Reactive Flow:** Orchestrated Event-Driven Flow: We utilize a Centralized Dispatcher pattern to manage side effects. When an article is published, a single event is fired, which is captured by a primary orchestrator. This orchestrator is responsible for dispatching granular, isolated jobs to the queue, ensuring a controlled, sequential, and highly maintainable notification lifecycle." -->

* **Contextual Intelligence:** Finally, we use **Contextual Binding** and the **Strategy Pattern** to dynamically resolve notification channels (`Email` for writers, `Database` for admins), allowing the system to make "intelligent" decisions based on the execution context.
*  **Performance & Scalability:**
To ensure the system remains highly responsive under heavy traffic, we have implemented several optimization strategies:

 1-Atomic Caching: We utilize `Atomic Locks` combined with `Redis` to mitigate the *Cache Stampede* phenomenon. This ensures that even under a load of 300+ concurrent requests, only a single process rebuilds the cache, while others wait or serve stale data, preventing database collapse.
 2-Intelligent Rate Limiting: Global and route-specific `Rate Limiting` is enforced to protect system resources from abuse and ensure fair usage across all API consumers.
 3-Asynchronous Processing: Heavy-lifting tasks, such as notification dispatching and report generation, are offloaded to background `Queues`. This decouples user-facing requests from long-running logic, significantly reducing response latency.
---
## 🛠️ Administrative Commands

# Archive articles older than 30 days (default)

php artisan articles:archive

# Archive articles older than 60 days
php artisan articles:archive 60

# Preview the operation without making changes
php artisan articles:archive {days} --dry-run

The system includes built-in commands for manual report generation and system maintenance:

# Generate Reports: Trigger manual report generation using the integrated strategy engine:
      php artisan articles:report weekly_articles_report

      php artisan articles:report monthly_authors_activity_report
   
---
## 📦 Installation

```bash
git clone [https://github.com/your-username/newsroom.git](https://github.com/your-username/newsroom.git)
cd newsroom
composer install
cp .env.example .env
php artisan key:generate
# Ensure QUEUE_CONNECTION is set to 'database' or 'redis'
php artisan migrate
 ```
 
## 🔗 API Endpoints

### 🔐 Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/v1/auth/register` | Register a new user |
| `POST` | `/api/v1/auth/login` | Authenticate and get token |

### 📄 Articles (v1)
| Method | Endpoint | Auth | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/articles` | No | List all published articles |
| `POST` | `/api/v1/articles` | Yes | Create a new article |
| `PUT` | `/api/v1/articles/{article}` | Yes | Update an existing article |
| `GET` | `/api/v1/articles/{article}` | No if the article published |show article  |
| `DELETE`| `/api/v1/articles/{id}` | Yes | Delete an article |

### 📊 Admin Dashboard
| Method | Endpoint | Auth | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/dashboard/snapshot`| Admin | Get platform performance summary |
| `GET` | `/api/v1/admin/dashboard//trigger-report`| Admin | Get weekly report |

### 🌍 Articles (v2)
| Method | Endpoint | Auth | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v2/articles` | No | List articles (Advanced/Aggregated) |

### 👤 User Profile
| Method | Endpoint | Auth | Description |
| :--- | :--- | :--- | :--- |
| `PUT` | `/api/v1/profile` | Yes | Update user information |

## 📖 API Documentation
The API is fully documented using **ApiDog**. You can explore all endpoints, test requests in real-time, and view sample responses through our interactive documentation platform:

👉 **[View API Documentation & Testing Console](https://share.apidog.com/84f4286e-bb51-48e1-872f-a61d78241d62)**
