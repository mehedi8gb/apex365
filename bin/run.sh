#!/bin/bash
php artisan queue:work redis --queue=referral-chain
