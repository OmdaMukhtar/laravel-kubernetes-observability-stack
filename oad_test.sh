#!/usr/bin/env bash

BASE_URL="https://laravel-api.example.com"
DURATION=300   # seconds (5 minutes)
SLEEP_MIN=0.05
SLEEP_MAX=0.5

END_TIME=$((SECONDS + DURATION))

echo "Starting load test for $DURATION seconds..."

random_sleep() {
  awk -v min=$SLEEP_MIN -v max=$SLEEP_MAX 'BEGIN{srand(); print min+rand()*(max-min)}'
}

while [ $SECONDS -lt $END_TIME ]; do

  case $((RANDOM % 10)) in
    0|1|2|3)
      # ✅ Normal successful requests
      curl -s -o /dev/null "$BASE_URL/login"
      ;;
    4)
      curl -s -o /dev/null "$BASE_URL/register"
      ;;
    5|6)
      # ❌ 404 errors
      curl -s -o /dev/null "$BASE_URL/not-found-$RANDOM"
      ;;
    7)
      # 🐌 Slow endpoint simulation
      curl -s -o /dev/null "$BASE_URL/slow"
      ;;
    8)
      # ⚠️ Metrics endpoint (internal noise)
      curl -s -o /dev/null "$BASE_URL/metrics"
      ;;
    9)
      # 💥 Simulated server error (if exists)
      curl -s -o /dev/null "$BASE_URL/error"
      ;;
  esac

  sleep "$(random_sleep)"

done

echo "Load test completed."
