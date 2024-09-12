# Virtual_queues or NoQ

NoQ is a barebone system for **managing service queues** in establishments that require a lot of effort and time per customer, have high traffic and require physical presence. The system is partnered with a [mobile app](https://github.com/Dero1014/Virtual_queues_mobile) for customers and a [embedded QR scanner](https://github.com/Dero1014/Virtual_queues_QR) for physical application. 

The point of the system is to **give back time to consumers** by not having them wait in lines for hours but apply for a queue and go on with their daily business and only arrive when necessary. Additional features are given to the owners of the system such as reviewing the interests in perticular service, average time per service ect.

## Table of contents
-  [Features](#features)
-  [How it works](#how-it-works)
   -  [Website](#website)
      -  [Admin](#admin)
      -  [Personel](#personel)
      -  [User](#user)
   -  [Mobile App](#mobile_app)
   -  [QR scanner](#qr_scanner)
   -  [Questions](#questions)

## Features
From the main system (website) side as admin:
  - Register various establishments for service managment
  - Create, manage and overview various services for each establishments
  - Create and assign accounts for your personel
As User:
  - Register your account
  - Browse available establishments through the site and apply for a perticular service of an establishment
  - Get up to date feedback on the queue status such as number of people in queue and average wait time
As personel:
  - Work through a simple to use system to control the queue

Adding a QR scanner to the establishment:
  - Apply for a queue without the need of getting a ticket
  - Let your customers know they don't have to wait in line
  - Simple scan of a QR code and get on with your day

From the mobile app strictly for users:
  - all the benefits as through the website

## How it works
Originally the system was inteded as a main hub from which all companies/establishments that have queues can register too. But the system can be used for a singular entity that holds one or more establishments.

### Website
The website is the main hub for the admins and personel of establishments. An admin can register an establishment, when we say establishment we mean a place where services are provided, so in turn you could make several establishments that offer the same service but in different locations such as a bank being provided in different regions of the city.

#### Admin
The admin of a perticular establishment can add services and create logins with perticular links that are required for personel to access the needed establishment. After which he can review services such as traffic and average wait time. [IMAGE1 - Show infographic of service]

Adding personel logins is simple as adding services, it mearly requiers that you enter a desired ID for the personel in mind. After which you will be given a randomly generated link with a strong password for the user to login from, this link and password are unique and won't work one without the other. [IMAGE 2 - Show adding worker]

#### Personel
Personel login through the provided link and password given by the admin. Only through the same generated password and link can a user login. This is to ensure that one can't enter the system without the necessary link and password, both being randomly generated. Once inside they can pick which service for the establishment they want to handle and they get a list of customers to know how long is the line. [IMAGE 4 - Show working on service]

#### User
With no ties to an establishment a user can pick an establishment of it's choosing and pick a service from an establishment to queue up to. Once queued up it will see it's possition in the queue as well how long is the wait. [IMAGE 3 - Picking a queue and queue stats]

### Mobile App
The app is strictly made for the users of the service. It displays the same options and info as on website. With the addition that it can use a QR to show to a scanner to log them into the system.

### QR scanner
The QR scanner will scan QR from user to log them into the system, this saves on time for looking for an establishment and service is the user is already there.

## Questions

**Can I use this for my own projects/establishments?**
Feel free to try, this is a student project with only a month or two in the making so everything is very barebone. You would have to make your own SQL and server to get it up and running, and as the website doesn't contain any unique UI elements you would have to add something to the design of the website.

**Will there be updates?**
At the moment this repo is closed, unless you want to updated it on your own. I plan somewhere in the future making a modular and up to date version so it can be used anywhere with minimal effort of integration.

