# hotel-backend

[![GitHub repo size in bytes](https://img.shields.io/github/repo-size/hotel-reservation-systems/hotel-backend.svg)](https://github.com/hotel-reservation-systems/hotel-backend/releases)
[![GitHub commit activity the past week, 4 weeks, year](https://img.shields.io/github/commit-activity/w/hotel-reservation-systems/hotel-backend.svg)](https://github.com/hotel-reservation-systems/hotel-backend/commits)
[![GitHub language count](https://img.shields.io/github/languages/count/hotel-reservation-systems/hotel-backend.svg)](https://github.com/hotel-reservation-systems/hotel-backend/search?l=JavaScript&type=Code)
[![license](https://img.shields.io/github/license/hotel-reservation-systems/hotel-backend.svg)](https://popoway.mit-license.org/)
[![Build Status](https://travis-ci.org/hotel-reservation-systems/hotel-backend.svg?branch=master)](https://travis-ci.org/hotel-reservation-systems/hotel-backend)

This is the backend codebase for the hotel project.

## Database Design
[dump.sqlite.sql](https://github.com/hotel-reservation-systems/hotel-backend/blob/master/dump.sqlite.sql)

## Error Messages
`0` Success  
`1` Runtime error  
`4` Wrong HTTP request method  

`5` Logging in is required for this privileged action  
`6` "customer" role is required for this privileged action  
`7` "administrator" role is required for this privileged action  

`8` A new user may not be logged in until the current user has logged out  
`9` A new user may not be created until the current user has logged out  

`41` Missing username  
`42` Missing password  
`43` Missing role  
`44` Invalid password  
`45` No account found with this username  
`46` This username is already taken  
`47` Password must have at least 6 characters  
`48` Illegal role  

`51` Missing chain name  
`52` This chain name is already taken  

`54` Missing hotel name  
`55` This hotel name is already taken  
`56` Missing grid_i  
`57` Missing grid_j  
`58` Invalid grid_i  
`59` Invalid grid_j  
`60` There already exists one hotel at the same grid(i,j)  

`71` Missing hotel id  
`72` Missing room name  
`73` Missing rate  
`74` Missing price  
`75` Rate must be one of: ['cheap', 'moderate', 'deluxe']  
`76` Price must be greater or equal to zero  
`77` hotel_id does not exist or user does not own this hotel_id  
`78` This room name is already taken  
`79` A hotel may not have more than two cheap (or deluxe) rooms  

`81` Missing room_id  
`82` Missing from date  
`83` Missing to date  
`87` date_to must be greater than date_from  
`88` Room is not available at requested date period  

`91` Missing reservation id  
`92` Reservation does not exist
`93` You may not cancel other user's reservation
