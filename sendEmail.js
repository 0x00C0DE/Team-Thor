const mysql = require("mysql");
const https = require("https");
const nodemailer = require("nodemailer");
const fs = require("fs");

//fetch constants
const vars = JSON.parse(fs.readFileSync("./emailConst.json"));

//connect to the database
let con = mysql.createConnection({
	host: vars.SQL_HOST,
	user: vars.SQL_USER,
	database: vars.SQL_USER,
	password: vars.SQL_PASS
});
console.log("Connecting to the database");
con.connect((err)=>{
	if(err) throw err;
	//connection was successful
	console.log("Querying the database");
	let query = "SELECT locations.name as locName, Account.name as name, lat, lon, email ";
	query += "FROM locations JOIN Account ON Account.lid=locations.user_lid ";
	query += "WHERE locations.is_subscribed='YES'";
	con.query(query,(err,res)=>{	//get locations which users are subscribed to
		if(err) throw err;
		//query was successful
		con.end((err)=>{	//kill the database connection
			if(err) console.log("Could not close database connection:"+err.message);
			console.log("Closed database connection");
		});
		console.log("Sending emails");
		Object.keys(res).forEach((key)=>{
			let row = res[key];
			//fetch forecast with api
			let apiUrl = "https://api.openweathermap.org/data/2.5/onecall";
			apiUrl += "?lat="+row["lat"]+"&lon="+row["lon"];
			apiUrl += "&apikey="+vars.API_KEY;
			apiUrl += "&exclude=current,minutely";
			apiUrl += "&units=metric";
			https.get(apiUrl,(res)=>{
				let data="";
				res.on("data",(chunk)=>{
					data += chunk;
				});
				res.on("end",()=>{
					let forecast = JSON.parse(data);
					//send email to users
					let transporter = nodemailer.createTransport({
						service: vars.MAIL_SERVICE,
						auth: { user: vars.MAIL_USER, pass: vars.MAIL_PASS }
					});
					let mailOptions = {
						from: "Thor Weather <"+vars.MAIL_USER+">",
						to: row["email"],
						subject: row["locName"]+" Daily Forecast",
						html: createEmailHtml(row["locName"],forecast)
					};
					//console.log(mailOptions.html);
					transporter.sendMail(mailOptions, (err,info)=>{
						if(err) console.log("Failed to send email to "+row.email);
						else console.log("Successfully sent email to "+row.email);
					});
				});
			}).on("error", (err)=>{
				console.log("Failed to request forecast for "+row["locName"]+":"+err.message);
			});
		});
	});
});


function createEmailHtml(locationName,forecastJson){
	let message = "<html><head>";
	message += "<meta name='viewport' content='width=device-width'>";
	message += "<style>\n"+fs.readFileSync("./css/emailStyle.css")+"</style>";
	message += "</head>";
	message += "<div class='mainWrapper'>";
	message += "<h1 class='header'>"+locationName+" Forecast</h1>";
	let date = new Date();
	let month = "00"+String(date.getMonth()+1);
	month = month.substr(month.length-2);
	let day = "00"+String(date.getDate());
	day = day.substr(day.length - 2);
	let currDate = date.getFullYear()+"-"+month+"-"+day;
	message += "<div class='subheader'>"+currDate+"</div>";

	message += "<div class='forecastWrapper'>";

	message += "<div class='summaryWrapper'>";
	let weather = forecastJson.daily[0].weather[0].description.replace(/^\w/,c => c.toUpperCase());
	message += "<div class='weatherWrapper'>Weather: "+weather;
	message += "</div>";
	message += "<div class='tempMinMaxWrapper'>"
	//maximum temperature
	let tempMax = Math.round(parseFloat(forecastJson.daily[0].temp.max));
	message += "<div class='tempWrapper'>";
	message += "High: "+String(tempMax)+"C</div>";
	//minimum temperature
	let tempMin = Math.round(parseFloat(forecastJson.daily[0].temp.min));
	message += "<div class='tempWrapper'>";
	message += "Low: "+String(tempMin)+"C</div>";
	message += "</div>";
	//chance of precipitation
	let precipChance = Math.round(parseFloat(forecastJson.daily[0].pop) * 100);
	message += "<div class='rainWrapper'>";
	message += "Chance of precipitation: "+String(precipChance)+"%";
	message += "</div>";
	message += "</div></div>"

	message += "<div class='detailsWrapper'>";
	message += "<h3 class='detailsHeader'>Details</h3>";
	message += "<table class='detailsTable table'>";
	//sunrise time
	let sunrise = new Date(forecastJson.daily[0].sunrise*1000);
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/sunrise.png'></td>";
	message += "<td>Sunrise: "+getFormattedTime(sunrise)+"</td></tr>";
	//sunset time
	let sunset = new Date(forecastJson.daily[0].sunset*1000);
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/sunset.png'></td>";
	message += "<td>Sunset: "+getFormattedTime(sunset)+"</td></tr>";
	//atmospheric pressure
	let pressure = forecastJson.daily[0].pressure;
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/barometer-gauge.png'></td>";
	message += "<td>Pressure: "+pressure+" hPa</td></tr>";
	//humidity
	let humidity = forecastJson.daily[0].humidity;
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/moisture.png'></td>";
	message += "<td>Humidity: "+humidity+"%</td></tr>";
	//dewPoint
	let dewPoint = Math.round(forecastJson.daily[0].dew_point);
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/dew-point.png'></td>";
	message += "<td>Dew point: "+dewPoint+"C</td></tr>";
	//wind speed and direction
	let windSpeed = forecastJson.daily[0].wind_speed;
	let windDeg = forecastJson.daily[0].wind_deg;
	let windDir = "N";
	let dirOpts = [
		[337.5,22.5,"N"],
		[22.5,67.5,"NE"],
		[67.5,112.5,"E"],
		[112.5,157.5,"SE"],
		[157.5,202.5,"S"],
		[202.5,247.5,"SW"],
		[247.5,292.5,"W"],
		[292.5,337.5,"NW"]
	];
	for(let i=0; i<dirOpts.length; i++){
		if(windDeg > dirOpts[i][0] && windDeg <= dirOpts[i][1]){
			windDir = dirOpts[i][2];
			break;
		}
	}
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/wind.png'></td>";
	message += "<td>Wind: "+windSpeed+"m/s "+windDir+" ";
	message += "("+windDeg+"&deg;)</td></tr>";
	//cloud cover
	let clouds = forecastJson.daily[0].clouds;
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/cloud.png'></td>";
	message += "<td>Cloud cover: "+clouds+"%</td></tr>";
	//uv index
	let uv = Math.round(forecastJson.daily[0].uvi);
	message += "<tr class='detailRow tableRow'><td>";
	message += "<img src='https://img.icons8.com/ios/50/000000/sun.png'></td>";
	message += "<td>UV index: "+uv+"</td></tr>";
	message += "</table></div>";

	//hourly forecast
	message += "<div class='hourlyWrapper'>";
	message += "<h3 class='hourlyHeader'>Hourly Temperature</h3>";
	message += "<table class='hourlyTable table'>";
	message += "<tr class='hourlyRow tableRow'><th>Hour</th><th>Temperature</th><th>Feels like</th></tr>";
	let hours = forecastJson.hourly.slice(0,24);
	hours.forEach((elem,i) => {
		//todo: time zones
		let date = new Date(elem.dt*1000);
		let hour = String(date.getHours())+":00";
		if(date.getHours()<10) hour = "0"+hour;
		message += "<tr class='hourlyRow tableRow'><td>";
		message += hour;
		message += "</td><td>";
		message += Math.round(elem.temp)+"C";
		message += "</td><td>";
		message += Math.round(elem.feels_like)+"C";
		message += "</td></tr>";
	});
	message += "</table>";
	message += "</div>";

	message += "</div></div>";
	message += "</html>"
	return message;
}

function getFormattedTime(dateObj){
	let hours = dateObj.getHours();
	if(hours < 10) hours = "0" + hours;
	let minutes = dateObj.getMinutes();
	if(minutes < 10) minutes = "0" + minutes;
	let seconds = dateObj.getSeconds();
	if(seconds < 10) seconds = "0" + seconds;
	let timezone = 0 - (dateObj.getTimezoneOffset()/60);
	if(timezone >= 0) timezone = "+"+timezone;
	let str = hours+":"+minutes+":"+seconds+" (UTC"+timezone+")";
	return str;
}
