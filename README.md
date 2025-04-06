# Full Stack JS Dev Assignment 1 -- Zepto Apps

Creating font uploader and font grouper, and displaying the list of all fonts and font groups.

## Stack(s)

1. [Bootstrap](https://getbootstrap.com)

## Procedure

### Option 1: Using Docker (Recommended)

```bash
docker build -t msrumon/zepto-fullstack-assignment-1 .
```

```bash
docker run -p 8080:80 --name msrumon-zepto-fullstack-assignment-1 msrumon/zepto-fullstack-assignment-1
```

Now visit <http://127.0.0.1:8080>.

To stop the container and remove all artifacts,

```bash
docker stop msrumon-zepto-fullstack-assignment-1
```

```bash
docker rm msrumon-zepto-fullstack-assignment-1
```

```bash
docker rmi msrumon/zepto-fullstack-assignment-1
```

### Option 2: Using Local PHP Installation

> Make sure you have [Composer](https://getcomposer.org) installed.

```bash
composer i
```

```bash
php -S 127.0.0.1:8080 -t public/
```

Now visit <http://127.0.0.1:8080>.
