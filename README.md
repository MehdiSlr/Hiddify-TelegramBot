# VPN Sales System with Hiddify Panel

## Overview

This project is designed to facilitate the sale of VPN services using the Hiddify panel. It integrates with a Telegram bot to provide users with an interactive way to view, select, and purchase VPN plans. The bot supports various plans, including unlimited and limited data options, with different pricing tiers.

## Features

- <b>Interactive Telegram Bot:</b> Users can interact with the bot to view available VPN plans, select their desired plan, and proceed with payment.

- <b>Plan Options:</b>

    - <b>Normal Plans:</b> Various data plans with limited and unlimited options.

    - <b>VIP Plans:</b> Premium plans with additional benefits.

- <b>Payment Integration:</b> Users can select payment options directly from the bot.

- <b>Proforma Invoice Generation:</b> Automatically generates a proforma invoice with plan details and pricing.

## Setup and Installation

### Requirements

- PHP 7.4 or higher

- MySQL or compatible database

- Telegram Bot API Token

- Hiddify Panel setup

### Configuration

1. <b>Database Configuration:</b> Update the database connection settings in `serv_conf.php`.

2. <b>Telegram Bot Setup:</b>

    - Create a Telegram bot using [BotFather](https://t.me/BotFather).

    - Obtain the bot API token and configure it in your script.

3. <b>Hiddify Panel Setup:</b> Ensure Hiddify is properly set up and configured to handle VPN provisioning and management.

### Installation Steps

1. Clone the repository or download the project files.

```bash
git clone https://your-repository-url.git
```

2. Configure the database connection by editing `serv_conf.php` with your database details.

3. Set up your Telegram bot by replacing placeholders in the script with your bot API token.

4. Deploy the script on your web server or local environment.

## Usage

### Starting the Bot

1. Start the bot using a PHP server or deploy it on a web server with PHP support.

2. Interact with the bot via Telegram:

    - Use `/start` to view the main menu.

    - Navigate through options to view available VPN plans.

    - Select a plan to view details and proceed with payment.

### Plan Selection

- <b>Normal Plans:</b> Choose from various data limits with prices ranging from 50,000 to 130,000 Tomans.

- <b>VIP Plans:</b> Premium options with prices from 90,000 to 170,000 Tomans.

### Payment

- Users can select "Card to Card" payment and follow instructions provided by the bot.

## Proforma Invoice

After selecting a plan and proceeding with payment, a proforma invoice will be generated with the following details:

- Plan Name

- Price

- Description

## Troubleshooting

- <b>Database Issues:<b> Check your database connection settings and ensure the temp table exists.

- <b>Bot Errors:</b> Ensure your bot token is correctly configured and that the bot is running.

## Contributing

Feel free to contribute to this project by submitting pull requests or reporting issues. Ensure that all contributions are well-documented and tested.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.