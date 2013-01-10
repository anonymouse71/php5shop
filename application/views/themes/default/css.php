<?php defined('SYSPATH') or die('No direct script access.'); ?>
<style type="text/css">
* {
    margin: 0px;
    padding: 0px
}
img {
    border: 0px;
}
html {
    width: 100%;
    height: 100%;
}
body {
    min-width: 412px;
    width: 985px;
    margin: 0 auto;
    font-family: Tahoma;
    font-size: 11px;
    color: #565656;
    position: relative
}

#header {
    padding: 17px 0 0 47px;
}

#menu {
    float: left;
    width: 985px;
    list-style-type: none;
    padding: 0px;
    margin: 12px 0 12px -47px;
}

#menu li, #menu img {
    float: left;
}

#container {
    padding-left: 172px;
    padding-right: 238px;
    height: 100%
}

#container .column {
    position: relative;
    float: left;
}

#center {
    width: 100%;
    position: relative;
}

.banner {
    margin: 0 2px 0 1px;
    float: left
}

#content {
    padding: 18px 12px 50px 21px;
    float: left
}

#content p {
    padding: 3px 0 0 5px;
    margin: 0px;
}

.pad25 {
    padding-top: 25px;
}

.stuff {
    margin: 25px 0 0 0;
    float: left;
}

.item {
    width: 270px;
    height: 270px;
    float: left;
    margin: 0 0 15px 0
}

.item img {
    float: left;
}

.name {
    font-family: Tahoma;
    font-size: 12px;
    color: #4A4A4A;
    text-decoration: underline;
    font-weight: bold;
    margin: 15px 0 0 5px;
    float: left;
    width: 200px;
}

.item span {
    color: #E27C0E;
    font-weight: bold;
    font-size: 12px;
    display: block;
    width: 140px;
    float: left;
    padding: 7px 0 12px 5px
}

.name:visited, .reg:visited, .more:visited {
    text-decoration: underline
}

.name:hover, .reg:hover, .more:hover {
    text-decoration: none
}

#left {
    width: 172px;
    right: 172px;
    margin-left: -100%;
}

.block {
    width: 168px;
    border: 1px solid #C5C5C5;
    padding: 1px 1px 14px 1px;
    margin-bottom: 4px;
}

#navigation {
    width: 168px;
    margin: 0px;
    padding: 0px;
}

#navigation li {
    list-style-type: none;
    line-height: 17px;
    padding: 0 0 0 13px;
}

#navigation a {
    color: #565656;
    text-decoration: none
}

.color {
    background-color: #EBEBEB
}

#right {
    width: 238px;
    margin-right: -238px;
}

.rightblock {
    padding: 0 0 0 14px
}

.blocks {
    width: 218px;
    background-image: url(images/bg.gif);
    background-position: top left;
    background-repeat: repeat-y;
    margin: 0 0 2px 0;
}

.line {
    display: block;
    float: left;
    line-height: 19px;
    padding: 5px 0 0 0;
    margin: 0px;
}

.blocks span {
    font-size: 11px;
    font-weight: bold;
    display: block;
    float: left;
    width: 68px;
    text-align: right;
    padding: 0 7px 0 0
}

.blocks #comparediv span {
    font-weight: bold;
    display: block;
    float: none;
    width: 200px;
    text-align: left;
    text-decoration: underline;
    padding: 0 7px 0 9px
}

.blocks input {
    width: 130px;
    height: 15px;
    float: left;
    border-top: 2px inset #808080;
    border-left: 2px inset #808080;
    border-right: 1px solid #CDCDCD;
    border-bottom: 1px solid #CDCDCD
}

#lastNews{
    padding: 10px;
    background-image: url(images/bg.gif);
    background-position: top left;
    background-repeat: repeat-y;
}

#news {
    padding: 0 5px 25px 13px;
    float: left;
}

#right .date {
    display: block;
    width: 100px;
    line-height: 19px;
    margin: 11px 0 12px 0;
    text-align: center;
    font-family: Arial;
    font-size: 12px;
    font-weight: normal;
    color: #272727;
    background-image: url(images/date.gif);
    background-position: top left;
    background-repeat: no-repeat;
}

#news p {
    display: block;
    float: left;
    width: 195px;
}

.more {
    display: block;
    float: left;
    color: #0283DD;
    text-decoration: underline;
    margin: 15px 0 0 0
}

.reg {
    color: #0283DD;
    text-decoration: underline;
    margin: 0 11px;
}

.center {
    width: 218px;
    text-align: center
}

.pad20 img {
    margin-top: 15px;
}

#footer {
    clear: both;
    border-top: 3px solid #B7C1C4;
    padding: 8px 0 17px 0;
    text-align: center;
    color: #323232
}

#footer a {
    color: #323232;
    text-decoration: none;
    margin: 0 3px;
}

#footer .terms {
    color: #0283DD
}

#footer p {
    padding: 10px 0 0 0
}

#footer #bft {
    color: #8E190B;
    text-decoration: underline;
    margin: 0px
}

#footer #bft:visited {
    text-decoration: underline
}

#footer #bft:hover {
    text-decoration: none
}

.float {
    float: left;
    margin-right: 164px;
}

.topblock1 {
    background-image: url(images/block2bg.gif);
    background-position: top left;
    background-repeat: no-repeat;
    width: 218px;
    height: 46px;
    padding: 15px 1px 0 30px;
    margin-bottom: 10px;
    margin-top: 10px;
    float: left;
    font-family: Tahoma;
    font-size: 11px;
    color: #5B5B5B;
    font-weight: bold
}

.topblock1 select {
    width: 128px;
    margin: 1px 0;
    border: 2px inset #CDCDCD;
    font-size: 11px
}

.topblock2 a {
    border: 1px solid #C0D6DE;
    margin: 5px 4px 0 0;
    float: left
}

.topblock2 {
    background-image: url(images/block2bg.gif);
    background-position: top left;
    background-repeat: no-repeat;
    width: 218px;
    height: 46px;
    padding: 15px 1px 0 24px;
    float: left;
    font-family: Tahoma;
    font-size: 11px;
    color: #5B5B5B;
    font-weight: bold
}

.shopping {
    float: left;
    padding: 3px 12px 0 0
}

.topblock2 p {
    line-height: 15px;
}

.topblock2 span {
    font-weight: normal;
}

.topblock2 #CartItems {
    color: #3288A2
}

#about {
    width: 517px;
    padding: 0 0 0 5px;
    float: left;
    margin: -10px 0 0 0;
}

.tree {
    width: 100%;
    height: 20px;
    border-bottom: 1px solid #BABABA;
    padding: 0 0 3px 0;
}

.tree a {
    color: #4A4A4A;
    text-decoration: underline
}

.tree a:visited {
    text-decoration: underline
}

.tree a:hover {
    text-decoration: none
}

.photos {
    width: 227px;
    float: left;
    padding: 25px 17px 0 0
}

.moreph {
    display: block;
    background-image: url(images/morebg.gif);
    background-position: top left;
    background-repeat: no-repeat;
    width: 92px;
    line-height: 17px;
    color: #FFF;
    text-decoration: none;
    padding: 0 0 0 14px;
    margin: 0 0 20px 18px
}

.comments {
    background-image: url(images/bulb.jpg);
    background-position: top left;
    background-repeat: no-repeat;
    padding: 0 0 5px 29px;
    margin: 0 0 0 18px;
    color: #0283DD;
    line-height: 25px;
    text-decoration: underline
}

.comments:visited {
    text-decoration: underline
}

.comments:hover {
    text-decoration: none
}

.description {
    width: 253px;
    float: left;
    padding: 25px 0 0 17px;
    position: relative
}

#about .description p {
    padding: 0 0 15px 0;
}

.description u {
    font-size: 12px;
    color: #4A4A4A;
    font-weight: bold
}

.price {
    position: absolute;
    top: 28px;
    right: 0px;
    color: #E27C0E;
    font-size: 12px;
    font-weight: bold
}

#features li {
    list-style-type: none;
    line-height: 17px;
    padding: 0 0 0 7px;
    width: 180px;
}

#features span {
    width: 150px;
    display: block;
    float: left;
}

#about button {
    background-image: url(images/add.gif);
    background-position: top left;
    background-repeat: no-repeat;
    border: 0px;
    float: left;
    font-weight: bold;
    width: 104px;
    font-family: Tahoma;
    font-size: 11px;
    color: #5B5B5B;
    margin: 21px 3px 0 0;
    padding: 4px 0 6px 0
}

.carts {
    padding: 21px 0 0 0;
    float: left
}

    /*Modal*/
#modal-content {
    display: none;
}

    /* IE 6 hacks */
#simplemodal-container a.modalCloseImg {
    background: none;
    right: -14px;
    width: 22px;
    height: 26px;
    filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src = 'images/x.png', sizingMethod = 'scale');
}

    /* Overlay */
#simplemodal-overlay {
    background-color: #000;
    cursor: wait;
}

    /* Container */
#simplemodal-container {
    height: 220px;
    width: 300px;
    color: #bbb;
    background-color: #333;
    border: 4px solid #444;
    padding: 12px;
}

#simplemodal-container code {
    background: #141414;
    border-left: 3px solid #65B43D;
    color: #bbb;
    display: block;
    margin-bottom: 12px;
    padding: 4px 6px 6px;
}

#simplemodal-container a {
    color: #ddd;
}

#simplemodal-container a.modalCloseImg {
    background: url(images/x.png) no-repeat;
    width: 25px;
    height: 29px;
    display: inline;
    z-index: 3200;
    position: absolute;
    top: -15px;
    right: -16px;
    cursor: pointer;
}

#simplemodal-container #basic-modal-content {
    padding: 8px;
}

.nextphoto {
    position: fixed;
    top: 105px;
    left: 450px;
    color: #FFFFFF;
}

.nfrom {
    position: fixed;
    top: 115px;
    left: 300px;
    color: #FFFFFF;
}

.post a {
    color: #808080;
    text-decoration: none;
}

.post {
    font-size: 12px;
    width: 550px;
}

.post .date {
    font-size: 10px;
}

a {
    color: #3E91DE;
    text-decoration: none;
}

.content {
    padding-top: 10px;
}

.content ul, ol {
    margin-left: 30px;
}

.date {
    text-align: right;
}

.left70 {
    padding-left: 70px;
}

.imgzoom, .imgcompare, .imgcart {
    cursor: pointer;
}

#comparebox {
    margin-left: 15px;
}

.hdn {
    float: left;
    display: none;
}

.count {
    float: left;
}

input, select {
    border: 1px;
    border-color: gray;
    border-style: solid;
    font-family: Verdana;
    font-size: 10px;
}

#sape, #sape a {
    color: #F3AE69;
}

#toTop {
    /* === jQ TOP === */
    position: fixed;
    background: #f1f1f1;
    border: 1px solid #cccccc;
    top: 10px;
    left: 10px;
    width: 70px;
    text-align: center;
    padding: 5px;
    color: #4A4A4A;
    cursor: pointer;
    text-decoration: none;

}

.shadow {
    /* тень */
    -moz-box-shadow: 2px 2px 5px #969696; /* Firefox 3.5+ */
    -webkit-box-shadow: 2px 2px 5px #969696; /* Safari, Chrome */
    filter: progid:DXImageTransform.Microsoft.Shadow(color = '#969696', Direction = 145, Strength = 5); /* Все версии IE */
}

.opacity90 {
    /* прозрачность 90 */
    filter: progid:DXImageTransform.Microsoft.Alpha(opacity = 90);
    filter: alpha(opacity = 90);
    -moz-opacity: 0.9;
    -khtml-opacity: 0.9;
    opacity: 0.9;
}

.level0 {
    padding-top: 5px;
}

.level1 {
    padding-left: 15px;
    padding-top: 5px;
}

.level2 {
    padding-left: 30px;
    padding-top: 5px;
}

.level3 {
    padding-left: 45px;
    padding-top: 5px;
}

.level4 {
    padding-left: 60px;
    padding-top: 5px;
}

.whsError {
    color: orangered;
    float: right;
    font-weight: bold;
    cursor: pointer
}

#showHeaderCompare {
    color: red;
    text-align: center;
}

#topTitle {
    margin-top: 48px;
    width: 900px;
    position: relative;
    left: -100px;
}
</style>