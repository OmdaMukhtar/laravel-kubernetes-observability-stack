## Deploy Laravel Applicaiton on K8S Cluster


---
## Project Tree
```
laravel-k8s/
├── app/
├── docker/
│   ├── Dockerfile
│   └── nginx.conf
├── k8s/
│   ├── namespace.yaml
│   ├── configmap.yaml
│   ├── nginx-configmap.yaml
│   ├── secret.yaml
│   ├── mysql/
│   │   ├── mysql-statefulset.yaml
│   │   ├── mysql-service.yaml
│   │   └── mysql-pvc.yaml
│   ├── laravel/
│   │   ├── deployment.yaml
│   │   ├── service.yaml
│   │   ├── service-monitor.yaml
│   │   └── ingress.yaml
│   └── storageclass.yaml
├── .env.example
└── README.md
```
---
## Steps

- Install or clone laravel into app folder
- Generate key and stored on configmap
  - login into any pods of the laravel deployment and run this

```bash
kubectl -n laravel-prod exec -it laravel-api-558f6f599d-6kzqv -- bash
php artisan key:generate --show
php artisan migrate
kubectl -n laravel-prod rollout restart deployment laravel-api
```

- setup login & register componenets of laravel-api
Step 1: Pull PHP + Composer Image
```bash
docker run --rm -it -v $(pwd)/app:/app -w /app composer:2 bash
composer install --no-dev --optimize-autoloader

Step2: Install Laravel Breeze:
composer require laravel/breeze --dev
php artisan breeze:install blade
```

Step 3: Build Frontend Assets with Node
```bash
docker run --rm -it -v $(pwd)/app:/app -w /app node:20 bash
npm install
npm run build
```

## Issue of session
``SESSION_DRIVER: database #file ``
as you know when the session driver is a file, user keep logout when the pod restarted or killed
and started on other node, thise issue solved by set the session_driver database or redis or other machinisme.




