# Virgosoft Exchange Engine

A limit-order exchange mini engine built with Laravel 13, Vue 3, Redis, MySQL and Pusher.

## Tech Stack
- **Backend:** Laravel 13, Sanctum, Redis (queues + cache + sessions), MySQL
- **Frontend:** Vue 3 (Composition API), Pinia, Tailwind CSS, Laravel Echo, Pusher
- **Real-time:** Pusher Channels

## Requirements
- PHP 8.3+
- Node.js 18+
- MySQL
- Redis (Memurai on Windows)
- Composer
- Pusher account

## Backend Setup

```bash
cd virgosoft-exchange-engine
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials, Redis config and Pusher keys.

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

Start the queue worker in a separate terminal:
```bash
php artisan queue:work redis --queue=orders
```

## Frontend Setup

```bash
cd frontend
npm install
cp .env.example .env
```

Update `frontend/.env` with your Pusher keys:
```env
VITE_PUSHER_APP_KEY=your_key
VITE_PUSHER_APP_CLUSTER=mt1
```

```bash
npm run dev
```

## Test Credentials
- **Email:** anthonynnanna@virgosoft.com
- **Password:** password

Other seeded users: check the `users` table. All passwords are `password`.

## Notes
- Commission is 1.5% of trade value, deducted from buyer's USD balance at order placement
- Asset commission is 1.5% of token amount, deducted from seller's USD balance at order placement
- Full match only — orders must have matching symbol, price compatibility and exact amount
- Redis handles queues, cache and sessions
- Pusher broadcasts `OrderMatched` events to both buyer and seller on trade execution
- Amount matching uses strict equality. In production, tolerance-based comparison would handle floating point edge cases