html {
	overflow-y: scroll;
	margin: 0;
	padding: 0;
}
body {
	background-color: #fff;
	color: #333333;
	font-family: Arial, Helvetica, sans-serif;
	margin: 0px;
	padding: 0px;
}
body, td, th, input, textarea, select, a {
	font-size: 12px;
}

h1, .welcome {
	color: #333;
	font-family: Georgia,"Times New Roman",serif;
	margin-top: 0px;
	margin-bottom: 20px;
	font-size: 32px;
	font-weight: normal;
	text-shadow: 0 0 1px rgba(0, 0, 0, .01);
}
h2 {
	color: #000000;
	font-size: 16px;
	margin-top: 0px;
	margin-bottom: 5px;
}
p {
	margin-top: 0px;
	margin-bottom: 20px;
}

 

a, a:visited, a b {
	color: #000;
	text-decoration: none;
	cursor: pointer;
}
a:hover {
	color: #c74242;
	text-decoration: underline;
}
a img {
	border: none;
}
form {
	padding: 0;
	margin: 0;
	display: inline;
}
input[type='text'], input[type='password'], textarea {

	
	background: #fff;
padding: 10px 16px;
font-size: 19px;
line-height: 1.33;

}


}
select {
	background: #F8F8F8;
	border: 1px solid #CCCCCC;
	padding: 2px;
}
label {
	cursor: pointer;
}
/* layout */
#container {
	width: 980px;
	margin-left: auto;
	margin-right: auto;
	text-align: left;
        padding:0px 10px;
	 
}
#column-left {
	float: left;
	width: 180px;
}
#column-right {
	float: right;
	width: 180px;
}
#content {
	min-height: 400px;
	margin-bottom: 25px;
}
#column-left + #column-right + #content, #column-left + #content {
	margin-left: 195px;
}
#column-right + #content {
	margin-right: 195px;
}
/* header */
#header {
	height: 210px;
	position: relative;
	z-index: 99;
 
}
#header #logo {
	position: absolute;
	top: 25px;
	left: 15px;
}  
#header #search {
	position: absolute;
	top: 100px;
	left: 0px;
	width: 930px;
	padding:25px;
	margin-bottom:25px;
	z-index: 15;
	background:#2BBBD8;
}
/* #header .button-search {
	position: absolute;
	left: 561px;
	background: url('../image/button-search.png') center center no-repeat;
	width: 38px;
	height: 49px;
	border-left: 1px solid #CCC;
	cursor: pointer;
}
#header #search input {
	background:#fff;
	padding: 10px 16px;
	font-size: 19px;
	line-height: 1.33;
	width:600px;
        border:5px solid #ddd;
 
 
} */
#header #welcome {
	position: absolute;
	top: 47px;
	right: 0px;
	z-index: 5;
	width: 298px;
	text-align: right;
	color: #999999;
}
#header .links {
	position: absolute;
	right: 0px;
	bottom: 3px;
	font-size: 10px;
	padding-right: 10px;
}
#header .links a {
	float: left;
	display: block;
	padding: 0px 0px 0px 7px;
	color: #38B0E3;
	text-decoration: none;
	font-size: 12px;
}
#header .links a + a {
	margin-left: 8px;
	border-left: 1px solid #CCC;
}
/* menu */
#menu {
	background: #585858;
	border-bottom: 1px solid #000000;
	height: 37px;
	margin-bottom: 15px;
	
	padding: 0px 5px;
}
#menu ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
#menu > ul > li {
	position: relative;
	float: left;
	z-index: 20;
	padding: 6px 5px 5px 0px;
}
#menu > ul > li:hover {
}
#menu > ul > li > a {
	font-size: 13px;
	color: #FFF;
	line-height: 14px;
	text-decoration: none;
	display: block;
	padding: 6px 10px 6px 10px;
	margin-bottom: 5px;
	z-index: 6;
	position: relative;
}
#menu > ul > li:hover > a {
	background: #000000;

}
#menu > ul > li > div {
	display: none;
	background: #FFFFFF;
	position: absolute;
	z-index: 5;
	padding: 5px;
	border: 1px solid #000000;
	-webkit-border-radius: 0px 0px 5px 5px;
	-moz-border-radius: 0px 0px 5px 5px;
	-khtml-border-radius: 0px 0px 5px 5px;
	border-radius: 0px 0px 5px 5px;
	background: url('../image/menu.png');
}
#menu > ul > li:hover > div {
	display: table;
}
#menu > ul > li > div > ul {
	display: table-cell;
}
#menu > ul > li ul + ul {
	padding-left: 20px;
}
#menu > ul > li ul > li > a {
	text-decoration: none;
	padding: 4px;
	color: #FFFFFF;
	display: block;
	white-space: nowrap;
	min-width: 120px;
}
#menu > ul > li ul > li > a:hover {
	background: #000000;
}
#menu > ul > li > div > ul > li > a {
	color: #FFFFFF;
}
.breadcrumb {
	color: #CCCCCC;
	margin-bottom: 10px;
	display:none;
}
.success, .warning, .attention, .information {
	padding: 10px 10px 10px 33px;
	margin-bottom: 15px;
	color: #555555;
	
}
.success {
	background: #EAF7D9 url('../image/success.png') 10px center no-repeat;

	
}
.warning {
	background: #FFD1D1 url('../image/warning.png') 10px center no-repeat;
	
	
}
.attention {
	background: #FFF5CC url('../image/attention.png') 10px center no-repeat;
	
	
}
.success .close, .warning .close, .attention .close, .information .close {
	float: right;
	padding-top: 4px;
	padding-right: 4px;
	cursor: pointer;
}
.required {
	color: #FF0000;
	font-weight: bold;
}
.error {
	display: block;
	color: #FF0000;
}
.help {
	color: #999;
	font-size: 10px;
	font-weight: normal;
	font-family: Verdana, Geneva, sans-serif;
	display: block;
}
table.form {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 20px;
}
table.form tr td:first-child {
	width: 150px;
}
table.form > * > * > td {
	color: #000000;
}
table.form td {
	padding: 4px;
}
input.large-field, select.large-field {
	width: 300px;
}
table.list {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	margin-bottom: 20px;
}
table.list td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}
table.list thead td {
	background-color: #EFEFEF;
	padding: 0px 5px;
}
table.list thead td a, .list thead td {
	text-decoration: none;
	color: #222222;
	font-weight: bold;
}
table.list tbody td {
	padding: 0px 5px;
}
table.list .left {
	text-align: left;
	padding: 7px;
}
table.list .right {
	text-align: right;
	padding: 7px;
}
table.list .center {
	text-align: center;
	padding: 7px;
}
table.radio {
	width: 100%;
	border-collapse: collapse;
}
table.radio td {
	padding: 5px;
}
table.radio td label {
	display: block;
}
table.radio tr td:first-child {
	width: 1px;
}
table.radio tr td:first-child input {
	margin-top: 1px;
}
table.radio tr.highlight:hover td {
	background: #F1FFDD;
	cursor: pointer;
}
.pagination {
	border-top: 1px solid #EEEEEE;
	padding-top: 8px;
	display: inline-block;
	width: 100%;
	margin-bottom: 10px;
}
.pagination .links {
	float: left;
}
.pagination .links a {
	display: inline-block;
	border: 1px solid #EEEEEE;
	padding: 4px 10px;
	text-decoration: none;
	color: #A3A3A3;
}
.pagination .links b {
	display: inline-block;
	border: 1px solid #269BC6;
	padding: 4px 10px;
	font-weight: normal;
	text-decoration: none;
	color: #269BC6;
	background: #FFFFFF;
}
.pagination .results {
	float: right;
	padding-top: 3px;
}
/* button */
a.button, input.button, input.button2 {
	cursor: pointer;        
        background: #F78D3F;
    color: #fff;
    text-shadow: 0 -1px 1px rgba(0,0,0,0.5);
    line-height: 20px;
    padding: 0 3px;
    border: 1px solid #ddd;
    border-radius:3px;
    
    
    
}
a.button {
	display: inline-block;
	text-decoration: none;
	padding: 6px 10px 6px 10px;
}
input.button {
	margin: 5px;
	border: 0;
	height: 24px;
padding: 2px 5px;
}

input.button2{
	margin: 5px;
	border: 0;
	height: 24px;
background: #2BBBD8;
line-height: 20px;
padding: 2px 5px;
}
#mcimage{
	border: 2px solid #000∫;
	
}

a.button:hover, input.button:hover {
	background-position: 0px -24px;
	background: #AAA!important;
	
}
a.button:hover, input.button2:hover {
	background-position: 0px -24px;
	background: red!important;
	
}
.buttons {
	background: #FFFFFF;
	overflow: auto;
	padding: 3px;
	margin-bottom: 20px;
}
.buttons .left {
	float: left;
	text-align: left;
}
.buttons .right {
	float: right;
	text-align: right;
}
.buttons .center {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}
.htabs {
	height: 30px;
	line-height: 16px;
	border-bottom: 1px solid #DDDDDD;
}
.htabs a {
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
	background: #FFFFFF url('../image/tab.png') repeat-x;
	padding: 7px 15px 6px 15px;
	float: left;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 13px;
	font-weight: bold;
	text-align: center;
	text-decoration: none;
	color: #000000;
	margin-right: 2px;
	display: none;
}
.htabs a.selected {
	padding-bottom: 7px;
	background: #FFFFFF;
}
.tab-content {
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
	padding: 10px;
	margin-bottom: 20px;
	z-index: 2;
	overflow: auto;
}
/* box */
.box {
	margin-bottom: 20px;
 
}
.box .box-heading {
	
	background:#F78D3F;
	padding: 8px 10px 7px 10px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	color:#fff;
	 
}
.box .box-content {
	
background:#eeeeee;
	padding: 10px;
	 
}
.bf-count {
    background: none repeat scroll 0 0 #F78D3F!important;
	}

 

/* box products */
.box-product {
	width: 100%;
	overflow: auto;
}
.box-product > div {
	width: 130px;
	display: inline-block;
	vertical-align: top;
	margin-right: 20px;
	margin-bottom: 20px;
}
#column-left + #column-right + #content .box-product > div {
	width: 119px;
}
.box-product .image {
	display: block;
	margin-bottom: 0px;
}
.box-product .image img {
	padding: 3px;
	border: 1px solid #E7E7E7;
}
.box-product .name a {
	color: #38B0E3;
	font-weight: bold;
	text-decoration: none;
	display: block;
	margin-bottom: 4px;
}
.box-product .price {
	display: block;
	font-weight: bold;
	color: #333333;
	margin-bottom: 4px;
}
.box-product .price-old {
	color: #F00;
	text-decoration: line-through;
}
.box-product .price-new {
	font-weight: bold;
}
.box-product .rating {
	display: block;
	margin-bottom: 4px;
}
/* box category */
ul.box-category, ul.box-category ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
ul.box-category > li:first-child {
	padding: 0px 8px 8px 0px;
}
ul.box-category > li {
	padding: 8px 8px 8px 0px;
}
ul.box-category > li + li {
	border-top: 1px solid #EEEEEE;
}
ul.box-category > li > a {
	text-decoration: none;
	color: #333;
}
ul.box-category > li ul {
	display: none;
}
ul.box-category > li a.active {
	font-weight: bold;
}
ul.box-category > li a.active + ul {
	display: block;
}
ul.box-category > li ul > li {
	padding: 5px 5px 0px 10px;
}
ul.box-category > li ul > li > a {
	text-decoration: none;
	display: block;
}
ul.box-category > li ul > li > a.active {
	font-weight: bold;
}
/* box filter */
ul.box-filter, ul.box-filter ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
ul.box-filter span {
	font-weight: bold;
	border-bottom: 1px solid #EEEEEE;	
	display: block;
	padding-bottom: 5px;
	margin-bottom: 8px;
}
ul.box-filter > li ul {
	padding-bottom: 10px;
}
/* content */
#content .content {
	padding: 10px;
	overflow: auto;
	margin-bottom: 20px;

}
#content .content .left {
	float: left;
	width: 49%;
}
#content .content .right {
	float: right;
	width: 49%;
}
/* category */
.category-info {
	overflow: auto;
	margin-bottom: 10px;
}
.category-info .image {
	float: left;
	padding: 5px;
	margin-right: 15px;
	border: 1px solid #E7E7E7;
}
.category-list {
	overflow: auto;
	margin-bottom: 20px;
}
.category-list ul {
	float: left;
	width: 18%;
}
/* manufacturer */
.manufacturer-list {
	background: #eeeeee;
	overflow: auto;
	margin-bottom: 20px;
}
.manufacturer-heading {
	background:#F78D3F;
	font-size: 15px;
	font-weight: bold;
	padding: 5px 8px;
	margin-bottom: 6px;
	color:#fff;
}
.manufacturer-content {
	padding: 8px;
}
.manufacturer-list ul {
	float: left;
	width: 25%;
	margin: 0;
	padding: 0;
	list-style: none;
	margin-bottom: 10px;
}
/* product */
.product-filter {
	padding-bottom: 5px;
	overflow: auto;
        padding:0px 0px 10px 5px;
}
.product-filter .display {
	margin-right: 15px;
	float: left;
	padding-top: 4px;
	color: #333;
}
.product-filter .display a {
	font-weight: bold;
}
.product-filter .sort {
	float: right;
	color: #333;
}
.product-filter .limit {
	margin-left: 15px;
	float: right;
	color: #333;
}
.product-compare {
	margin-bottom: 25px;
	font-weight: bold;
}
.product-compare a {
	text-decoration: none;
	font-weight: bold;
}
.product-list > div {
	overflow: auto;
	margin-bottom: 0px;
	padding:2px 8px;
	border:1px solid #ccc;
}
.product-list .right {
	float: right;
	margin-left: 15px;
}
.product-list > div + div {
	border-top: 0px solid #EEEEEE;
	
}
.product-list .image {
	float: left;
	margin-right: 10px;
}
.product-list .image img {
	padding: 3px;
	border: 1px solid #E7E7E7;
}
.product-list .name {
	margin-bottom: 3px;
}
.product-list .name a {
	color: #38B0E3;
	font-weight: bold;
	text-decoration: none;
}
.product-list .description {
	line-height: 15px;
	margin-bottom: 5px;
	color: #4D4D4D;
}
.product-list .rating {
	color: #7B7B7B;
}
.product-list .price {
	float: right;
	height: 50px;
	margin-left: 8px;
	text-align: right;
	color: #333333;
	font-size: 12px;
}
.product-list .price-old {
	color: #F00;
	text-decoration: line-through;
}
.product-list .price-new {
	font-weight: bold;
}
.product-list .price-tax {
	font-size: 12px;
	font-weight: normal;
	color: #BBBBBB;
}
.product-list .cart {
	margin-bottom: 3px;
}
.product-list .wishlist, .product-list .compare {
	margin-bottom: 3px;
}
.product-list .wishlist a {
	color: #333333;
	text-decoration: none;
	padding-left: 18px;
	display: block;
	background: url('../image/add.png') left center no-repeat;
}
.product-list .compare a {
	color: #333333;
	text-decoration: none;
	padding-left: 18px;
	display: block;
	background: url('../image/add.png') left 60% no-repeat;
}
.product-grid {
	width: 100%;
	overflow: auto;
}
.product-grid > div {
	width: 130px;
	display: inline-block;
	vertical-align: top;
	margin-right: 20px;
	margin-bottom: 15px;
}
#column-left + #column-right + #content .product-grid > div {
	width: 125px;
}
.product-grid .image {
	display: block;
	margin-bottom: 0px;
}
.product-grid .image img {
	padding: 3px;
	border: 1px solid #E7E7E7;
}
.product-grid .name a {
	color: #38B0E3;
	font-weight: bold;
	text-decoration: none;
	display: block;
	margin-bottom: 4px;
}
.product-grid .description {
	display: none;
}
.product-grid .rating {
	display: block;
	margin-bottom: 4px;
}
.product-grid .price {
	display: block;
	font-weight: bold;
	color: #333333;
	margin-bottom: 4px;
}
.product-grid .price-old {
	color: #F00;
	text-decoration: line-through;
}
.product-grid .price-new {
	font-weight: bold;
}
.product-grid .price .price-tax {
	display: none;
}
.product-grid .cart {
	margin-bottom: 3px;
}
.product-grid .wishlist, .product-grid .compare {
	margin-bottom: 3px;
}
.product-grid .wishlist a {
	color: #333333;
	text-decoration: none;
	padding-left: 18px;
	display: block;
	background: url('../image/add.png') left center no-repeat;
}
.product-grid .compare a {
	color: #333333;
	text-decoration: none;
	padding-left: 18px;
	display: block;
	background: url('../image/add.png') left center no-repeat;
}
/* Product */
.product-info {
	overflow: auto;
	margin-bottom: 20px;
}
.product-info > .left {
	float: left;
	margin-right: 15px;
}
.product-info > .left + .right {
	margin-left: 265px;
}
.product-info .image {
	border: 1px solid #E7E7E7;
	float: left;
	margin-bottom: 20px;
	padding: 10px;
	text-align: center;
}
.product-info .image-additional {
	width: 260px;
	margin-left: -10px;
	clear: both;
	overflow: hidden;
}
.product-info .image-additional img {
	border: 1px solid #E7E7E7;
}
.product-info .image-additional a {
	float: left;
	display: block;
	margin-left: 10px;
	margin-bottom: 10px;
}
.product-info .description {
	/* border-top: 1px solid #E7E7E7;
	border-bottom: 1px solid #E7E7E7; */
	padding: 5px 5px 10px 5px;
	margin-bottom: 10px;
	line-height: 20px;
	color: #4D4D4D;
}
.product-info .description span {
	color: #38B0E3;
}
.product-info .description a {
	color: #4D4D4D;
	text-decoration: none;
}
.product-info .price {
	overflow: auto;
	border-bottom: 1px solid #E7E7E7;
	padding: 0px 5px 10px 5px;
	margin-bottom: 10px;
	font-size: 15px;
	font-weight: bold;
	color: #333333;
}
.product-info .price-old {
	color: #F00;
	text-decoration: line-through;
}
.product-info .price-new {
}
.product-info .price-tax {
	font-size: 12px;
	font-weight: normal;
	color: #999;
}
.product-info .price .reward {
	font-size: 12px;
	font-weight: normal;
	color: #999;
}
.product-info .price .discount {
	font-weight: normal;
	font-size: 12px;
	color: #4D4D4D;
}
.product-info .options {
	border-bottom: 1px solid #E7E7E7;
	padding: 0px 5px 10px 5px;
	margin-bottom: 10px;
	color: #000000;
}
.product-info .option-image {
	margin-top: 3px;
	margin-bottom: 10px;
}
.product-info .option-image label {
	display: block;
	width: 100%;
	height: 100%;
}
.product-info .option-image img {
	margin-right: 5px;
	border: 1px solid #CCCCCC;
	cursor: pointer;
}
.product-info .cart {
	border-bottom: 1px solid #E7E7E7;
	padding: 0px 5px 10px 5px;
	margin-bottom: 20px;
	color: #4D4D4D;
}
.product-info .cart div > span {
	color: #999;
}
.product-info .cart .links {
	display: inline-block;
	vertical-align: middle;
}
.product-info .cart .minimum {
	padding-top: 5px;
	font-size: 11px;
	color: #999;
}
.product-info .review {
	color: #4D4D4D;
	border-top: 1px solid #E7E7E7;
	border-left: 1px solid #E7E7E7;
	border-right: 1px solid #E7E7E7;
	margin-bottom: 10px;
}
.product-info .review > div {
	padding: 8px;
	border-bottom: 1px solid #E7E7E7;
	line-height: 20px;
}
.product-info .review > div > span {
	color: #38B0E3;
}
.product-info .review .share {
	overflow: auto;
	line-height: normal;
}
.product-info .review .share a {
	text-decoration: none;
}
.review-list {
	padding: 10px;
	overflow: auto;
	margin-bottom: 20px;
	border: 1px solid #EEEEEE;
}
.review-list .author {
	float: left;
	margin-bottom: 20px;
}
.review-list .rating {
	float: right;
	margin-bottom: 20px;
}
.review-list .text {
	clear: both;
}
.attribute {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	margin-bottom: 20px;
}
.attribute thead td, .attribute thead tr td:first-child {
	color: #000000;
	font-size: 14px;
	font-weight: bold;
	background: #F7F7F7;
	text-align: left;
}
.attribute tr td:first-child {
	color: #000000;
	font-weight: bold;
	text-align: right;
	width: 20%;
 
}
.attribute td {
	padding: 7px;
	color: #4D4D4D;
	text-align: center;
	vertical-align: top;
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}






.compare-info {
	border-collapse: collapse;
	width: 100%;
        background:#fff;
	margin-bottom: 20px;
}
.compare-info thead td, .compare-info thead tr td:first-child {
	color: #000000;
	font-size: 18px;
	text-align: center;
        background:#fff;
        border:0px;
        
}
 


.compare-info tr td:first-child {
	color: #000000;
	font-weight: normal;
	text-align: right;
}
.compare-info td {
	padding: 7px;
	max-width: 200px;
	color: #4D4D4D;
	text-align: center;
	vertical-align: top;
                background:#dceaf1;
                border:2px solid #fff;
	 
}
.compare-info .name a {
	font-weight: bold;
       
}


 

.compare-info .price-old {
	font-weight: bold;
	color: #F00;
	text-decoration: line-through;
}
.compare-info .price-new {
	font-weight: bold;
}
/* wishlist */
.wishlist-info table {
	width: 100%;
	border-collapse: collapse;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
	margin-bottom: 20px;
}
.wishlist-info td {
	padding: 7px;
}
.wishlist-info thead td {
	color: #4D4D4D;
	font-weight: bold;
	background-color: #F7F7F7;
	border-bottom: 1px solid #DDDDDD;
}
.wishlist-info thead .image {
	text-align: center;
}
.wishlist-info thead .name, .wishlist-info thead .model, .wishlist-info thead .stock {
	text-align: left;
}
.wishlist-info thead .quantity, .wishlist-info thead .price, .wishlist-info thead .total, .wishlist-info thead .action {
	text-align: right;
}
.wishlist-info tbody td {
	vertical-align: top;
	border-bottom: 1px solid #DDDDDD;
}
.wishlist-info tbody .image img {
	border: 1px solid #DDDDDD;
}
.wishlist-info tbody .image {
	text-align: center;
}
.wishlist-info tbody .name, .wishlist-info tbody .model, .wishlist-info tbody .stock {
	text-align: left;
}
.wishlist-info tbody .quantity, .wishlist-info tbody .price, .wishlist-info tbody .total, .wishlist-info tbody .action {
	text-align: right;
}
.wishlist-info tbody .price s {
	color: #F00;
}
.wishlist-info tbody .action img {
	cursor: pointer;
}
.login-content {
	margin-bottom: 20px;
	overflow: auto;
}
.login-content .left {
	float: left;
	width: 48%;
}
.login-content .right {
	float: right;
	width: 48%
}
.login-content .left .content, .login-content .right .content {
	min-height: 190px;
}
/* orders */
.order-list {
	margin-bottom: 10px;
}
.order-list .order-id {
	width: 49%;
	float: left;
	margin-bottom: 2px;
}
.order-list .order-status {
	width: 49%;
	float: right;
	text-align: right;
	margin-bottom: 2px;
}
.order-list .order-content {
	padding: 10px 0px;
	display: inline-block;
	width: 100%;
	margin-bottom: 20px;
	border-top: 1px solid #EEEEEE;
	border-bottom: 1px solid #EEEEEE;
}
.order-list .order-content div {
	float: left;
	width: 33.3%;
}
.order-list .order-info {
	text-align: right;
}
.order-detail {
	background: #EFEFEF;
	font-weight: bold;
}
/* returns */
.return-list {
	margin-bottom: 10px;
}
.return-list .return-id {
	width: 49%;
	float: left;
	margin-bottom: 2px;
}
.return-list .return-status {
	width: 49%;
	float: right;
	text-align: right;
	margin-bottom: 2px;
}
.return-list .return-content {
	padding: 10px 0px;
	display: inline-block;
	width: 100%;
	margin-bottom: 20px;
	border-top: 1px solid #EEEEEE;
	border-bottom: 1px solid #EEEEEE;
}
.return-list .return-content div {
	float: left;
	width: 33.3%;
}
.return-list .return-info {
	text-align: right;
}
.return-product {
	overflow: auto;
	margin-bottom: 20px;
}
.return-name {
	float: left;
	width: 31%;
	margin-right: 15px;
}
.return-model {
	float: left;
	width: 31%;
	margin-right: 15px;
}
.return-quantity {
	float: left;
	width: 31%;
}
.return-detail {
	overflow: auto;
	margin-bottom: 20px;
}
.return-reason {
	float: left;
	width: 31%;
	margin-right: 15px;
}
.return-opened {
	float: left;
	width: 31%;
	margin-right: 15px;
}
.return-opened textarea {
	width: 98%;
	vertical-align: top;
}
.return-captcha {
	float: left;
}
.download-list {
	margin-bottom: 10px;
}
.download-list .download-id {
	width: 49%;
	float: left;
	margin-bottom: 2px;
}
.download-list .download-status {
	width: 49%;
	float: right;
	text-align: right;
	margin-bottom: 2px;
}
.download-list .download-content {
	padding: 10px 0px;
	display: inline-block;
	width: 100%;
	margin-bottom: 20px;
	border-top: 1px solid #EEEEEE;
	border-bottom: 1px solid #EEEEEE;
}
.download-list .download-content div {
	float: left;
	width: 33.3%;
}
.download-list .download-info {
	text-align: right;
}
/* cart */
.cart-info table {
	width: 100%;
	margin-bottom: 15px;
	border-collapse: collapse;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
}
.cart-info td {
	padding: 7px;
}
.cart-info thead td {
	color: #4D4D4D;
	font-weight: bold;
	background-color: #F7F7F7;
	border-bottom: 1px solid #DDDDDD;
}
.cart-info thead .image {
	text-align: center;
}
.cart-info thead .name, .cart-info thead .model, .cart-info thead .quantity {
	text-align: left;
}
.cart-info thead .price, .cart-info thead .total {
	text-align: right;
}
.cart-info tbody td {
	vertical-align: top;
	border-bottom: 1px solid #DDDDDD;
}
.cart-info tbody .image img {
	border: 1px solid #DDDDDD;
}
.cart-info tbody .image {
	text-align: center;
}
.cart-info tbody .name, .cart-info tbody .model, .cart-info tbody .quantity {
	text-align: left;
}
.cart-info tbody .quantity input[type='image'], .cart-info tbody .quantity img {
	position: relative;
	top: 4px;
	cursor: pointer;
}
.cart-info tbody .price, .cart-info tbody .total {
	text-align: right;
}
.cart-info tbody span.stock {
	color: #F00;
	font-weight: bold;
}
.cart-module > div {
	display: none;
}
.cart-total {
	border-top: 1px solid #DDDDDD;
	overflow: auto;
	padding-top: 8px;
	margin-bottom: 15px;
}
.cart-total table {
	float: right;
}
.cart-total td {
	padding: 3px;
	text-align: right;
}
/* checkout */
.checkout-heading {
	background: #F8F8F8;
	border: 1px solid #DBDEE1;
	padding: 8px;
	font-weight: bold;
	font-size: 13px;
	color: #555555;
	margin-bottom: 15px;
}
.checkout-heading a {
	float: right;
	margin-top: 1px;
	font-weight: normal;
	text-decoration: none;
}
.checkout-content {
	padding: 0px 0px 15px 0px;
	display: none;
	overflow: auto;
}
.checkout-content .left {
	float: left;
	width: 48%;
}
.checkout-content .right {
	float: right;
	width: 48%;
}
.checkout-content .buttons {
	clear: both;
}
.checkout-product table {
	width: 100%;
	border-collapse: collapse;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
	margin-bottom: 20px;
}
.checkout-product td {
	padding: 7px;
}
.checkout-product thead td {
	color: #4D4D4D;
	font-weight: bold;
	background-color: #F7F7F7;
	border-bottom: 1px solid #DDDDDD;
}
.checkout-product thead .name, .checkout-product thead .model {
	text-align: left;
}
.checkout-product thead .quantity, .checkout-product thead .price, .checkout-product thead .total {
	text-align: right;
}
.checkout-product tbody td {
	vertical-align: top;
	border-bottom: 1px solid #DDDDDD;
}
.checkout-product tbody .name, .checkout-product tbody .model {
	text-align: left;
}
.checkout-product tbody .quantity, .checkout-product tbody .price, .checkout-product tbody .total {
	text-align: right;
}
.checkout-product tfoot td {
	text-align: right;
	border-bottom: 1px solid #DDDDDD;
}
.contact-info {
	overflow: auto;
}
.contact-info .left {
	float: left;
	width: 48%;
}
.contact-info .right {
	float: left;
	width: 48%;
}
.sitemap-info {
	overflow: auto;
	margin-bottom: 40px;
}
.sitemap-info .left {
	float: left;
	width: 48%;
}
.sitemap-info .right {
	float: left;
	width: 48%;
}
/* footer */ 
#footer {
	clear: both;
	overflow: auto;
	min-height: 100px;
	padding: 20px;

	background: #F78D3F;
}
#footer h3 {
	color: #fff;
	font-size: 14px;
	margin-top: 0px;
	margin-bottom: 8px;
}
#footer .column {
	float: left;
	width: 25%;
	min-height: 100px;
}
#footer .column ul {
	margin-top: 0px;
	margin-left: 0px;
	padding-left: 0px;
}
#footer .column ul li {
	margin-bottom: 3px;
	list-style: none
}
#footer .column a {
	text-decoration: none;
	color: #fff;
}
#footer .column a:hover {
	text-decoration: underline;
}
#powered {
	margin-top: 5px;
	text-align: right;
	clear: both;
}
/* banner */
.banner div {
	text-align: center;
	width: 100%;
	display: none;
}
.banner div img {
	margin-bottom: 20px;
}



			
/* mouthpiece listing tables */

	.Tablemain
    {
        display: table;
	
		padding-bottom:3px;
		
    }
    
 
}


.Table
    {
        display: table;
		padding:0px;
                background:#ffcc00;
    }

 .theader, .theader1 {
                
                width: auto;
                
		padding: 10px 5px;
          
   
                 }
                 
                 
 .theader {
                background-color: #DDD;
                width: auto;
                
		padding: 10px 5px;
                 background:#ddd;
               
                       
           
   
                 }
                 
 
    .Title
    {
        display: table-caption;
        text-align: left;
    
        
    }
	
    .Heading
    {
        display: table-row;
        font-weight: bold;
        text-align: center;
	
		padding:0px;
    }
    .Row
    {
        display: table-row;
		padding:0px;
         
		 
    }
	    .Cell, .Cell1
    {
        display: table-cell;
		padding: 5px;
                     
		
    }
    .Cell
    {
         
       background:#f1f1f1;                
		
    }
    
   
	 
.bf-attr-group-header{
        display:none;
    }
    
#accuracy {
         
       background:#f1f1f1;   
      margin-bottom:0px; 
      padding:5px;          
		
    }
    
    
    
    /**
 * iOS 6 style switch checkboxes
 * by Lea Verou http://lea.verou.me
                 */

                :root input[type="checkbox"] { /* :root here acting as a filter for older browsers */
                        position: absolute;
                        opacity: 0;
                }

                :root input[type="checkbox"].ios-switch + div {
                        display: inline-block;
                        vertical-align: middle;
                        width: 3em;	height: 1em;
                        border: 1px solid rgba(0,0,0,.3);
                        border-radius: 999px;
                        margin: 0 .5em;
                        background: white;
                        background-image: linear-gradient(rgba(0,0,0,.1), transparent),
                                          linear-gradient(90deg, hsl(210, 90%, 60%) 50%, transparent 50%);
                        background-size: 200% 100%;
                        background-position: 100% 0;
                        background-origin: border-box;
                        background-clip: border-box;
                        overflow: hidden;
                        transition-duration: .4s;
                        transition-property: padding, width, background-position, text-indent;
                        box-shadow: 0 .1em .1em rgba(0,0,0,.2) inset,
                                    0 .45em 0 .1em rgba(0,0,0,.05) inset;
                        font-size: 150%; /* change this and see how they adjust! */
                }

                :root input[type="checkbox"].ios-switch:checked + div {
                        padding-left: 2em;	width: 1em;
                        background-position: 0 0;
                }

                :root input[type="checkbox"].ios-switch + div:before {
                        content: 'On';
                        float: left;
                        width: 1.65em; height: 1.65em;
                        margin: -.1em;
                        border: 1px solid rgba(0,0,0,.35);
                        border-radius: inherit;
                        background: white;
                        background-image: linear-gradient(rgba(0,0,0,.2), transparent);
                        box-shadow: 0 .1em .1em .1em hsla(0,0%,100%,.8) inset,
                                    0 0 .5em rgba(0,0,0,.3);
                        color: white;
                        text-shadow: 0 -1px 1px rgba(0,0,0,.3);
                        text-indent: -2.5em;
                }

                :root input[type="checkbox"].ios-switch:active + div:before {
                        background-color: #eee;
                }

                :root input[type="checkbox"].ios-switch:focus + div {
                        box-shadow: 0 .1em .1em rgba(0,0,0,.2) inset,
                                    0 .45em 0 .1em rgba(0,0,0,.05) inset,
                                    0 0 .4em 1px rgba(255,0,0,.5);
                }

                :root input[type="checkbox"].ios-switch + div:before,
                :root input[type="checkbox"].ios-switch + div:after {
                        font: bold 60%/1.9 sans-serif;
                        text-transform: uppercase;
                }

                :root input[type="checkbox"].ios-switch + div:after {
                        content: 'Off';
                        float: left;
                        text-indent: .5em;
                        color: rgba(0,0,0,.45);
                        text-shadow: none;

                }