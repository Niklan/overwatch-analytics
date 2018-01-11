<p align="center">
 <img src="https://rawgit.com/Niklan/overwatch-analytics/master/web/themes/custom/overwatch_analytics_theme/logo-dark.svg" align="center" alt="Overwatch Analytics">

 <p align="center">
   Website which analyze Overwatch competitive games.
 </p>
</p>

## What's this

This is experimental project not targeted for launch. I created it for learn new technologies inside Drupal:

- CSS Grids
- Drupal custom entities. There are several different custom entities defined and fully worked.
- Vue.js for custom forms inside Drupal with controller to handle his requests.
- Chart.js for beautiful chart on canvas.
- Multilingual Drupal 8 features and behaviour for custom entities.
- Drupal custom REST resources.
- Other things.

The primary target for this site is analytics for competitive games. You can enter your competitive games information such as group size, map you've been playing, heroes you played, starting side, result score, duration and final skill rating with notes. The site will analyze it in whole season and shows statistic for you with charts.

## What's done

- Battle.net OAuth integration.
    - Custom module for work with Battle.net API, which is very bad for Overwatch.
    - Primarily used for authentication and registration using your Battle.net account.
    - Parse user picture from official playoverwatch.com user profile and save it to user entity after login.
    - Add several custom hidden fields to User entity. They are store battletag and battle.net user id.
- Custom theme with very basic styling.
- Overwatch Analytics - custom module which parse some information from matches and do other custom things for site.
- Overwatch Hero - custom module with custom entity and helper service. Used for store game heroes information. Has no bundles.
- Overwatch Map - custom module with custom entity and helper service. Used for store game maps. Has no bundles.
- Overwatch Match - custom module with custom entity and helper service. Used for store information about matches. Has bundles.
- Overwatch Season - custom module with custom entity and helper service. This simple entity store information about competitive seasons of overwatch.

This site is currently fully functional and a lot of things working but lack of deep analytics. It's fully multilingual, works perfectly as expected.

This project is fully **just for fun**. This was really interesting for me, because implementing things from Overwatch game inside website is fun. I wanted to look how custom entities works in Drupal 8, what's their behavior and what they can do, their advantages and disadvantages, problems and catchups. And this is done, I just continue to do something, because it's really funny and challenging for now.

## Installation

Yes, you can fully install it and try.

1. git clone this repo in you web root.
2. Install all required packages via composer: `composer install`
3. Install config installer profile: `composer require drupal/config-installer`
4. Open you website domain and install Drupal as usual. The only thing you need to change - select Config Installer profile, not minimal or standard.
5. After it installs, you're done.

This installation process don't add any content. So you need to add by yourself. You can add it via new toolbar item "Overwatch'.

![Toolbar](https://i.imgur.com/HpFUrO7.png)

### Battle.net auth support.

1. Register on https://dev.battle.net/ and create an app.

    1.1 To work Battle.net OAuth your local site must have HTTPS. Self-signed is okay, but it must be.
    
    2.1 For "Register callback URL" enter: `https://YOURDOMAIN/bnet/callback`.

2. Get you key and secret.
3. Go to site Configurations > System > Battle.net OAuth settings
4. Enter secret and key.

## Screenshots

![Statistics page with analyze example](https://i.imgur.com/5BiauJu.png)
![Add competitive match page](https://i.imgur.com/yYzURmX.png)

## API

The site expose API to own needs. It's not for third-paty use, but it must be documented.

**WARNING!** Every request must have GET parameter `_format`. It can be `json` or `xml`. This is type in which result will be returned.

### Is user has rating in season

Checks, has user SR in particular season or not.

* **URL**

  /api/v1/is-user-has-rating-in-season

* **Method:**

  `GET`
  
*  **URL Params**

   **Required:**
 
   `sid=[integer]` - Season entity (!) ID.

   **Optional:**
 
   `uid=[integer]` - User ID. If not provided, current user will be used.

* **Data Params**

  None

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** `{ has_sr : true }`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{ message : "User is not found" }`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: '/api/v1/is-user-has-rating-in-season',
      dataType: 'json',
      data: {
        '_format': 'json',
        'sid': 8,
      },
      type : 'GET',
      success : response => {
        console.log(response);
      }  
    });
  ```

Overwatch © 2018 Blizzard Entertainment.