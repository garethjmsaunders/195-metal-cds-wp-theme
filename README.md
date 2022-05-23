# 195 metal CDs WordPress child theme

Child theme of the free version of [ImageGridly](https://cor.wordpress.org/themes/imagegridly/) used at [195metalcds.com](https://195metalcds.com).


## Description

Having been using ImageGridly for a few years, initially on the freely-hosted WordPress.com, I moved my music review blog to my own personal hosting account on [SiteGround](https://www.siteground.com/recommended?referrer_id=7062720) (referral link).

I had been manually updating a few of the listing pages (full list of reviews, listed by genres, and listed by scores). I decided to create a WordPress child theme to manage these automatically.


## Files

```
|--- functions.php
|--- README.md
|--- screenshot.png
|--- single.php
|--- style.css
|   
---\images
|   |--- 195-metal-cds-bleary.png
|   |--- 195-metal-cds.pub
|   |--- header-600.jpg
|   |--- v2osk-IHKBF23A_iw-unsplash.jpg
|       
----\template-parts
|   |--- content-none.php
|   |--- content-search.php
|   |--- content-single.php
|   |--- content.php
```

### Functions.php

Most of the heavy-lifting is done within the `functions.php` file which contains the following 'blocks' of code:

1. Child theme CSS
2. Remove 'posted on' from top of review post
3. Posts : review scores custom taxonomy : create new custom taxonomy
4. Posts : review score columns on admin screen
5. Post : add meta box for review score to posts
6. Page : full list shortcode `[fulllist]`
7. Page : genres shortcode `[genres]`
8. Page : scores shortcode `[scores]`


