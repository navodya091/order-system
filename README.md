# Orders System

## Setup
1. Clone repo
2. `composer install`
3. Configure `.env` (DB + Redis)
4. `php artisan migrate`
5. `php artisan serve`
6. `php artisan horizon`
7. Queue import: `php artisan orders:import orders.csv`

## Features
- CSV import with queued jobs
- Order workflow: reserve → pay → finalize
- Notifications logged to DB
- Refund handling & KPIs updated
- Redis leaderboard & KPIs
- API dashboard `/api/dashboard/kpis`
