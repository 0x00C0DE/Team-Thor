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
	let message = "<style>\n"+fs.readFileSync("./css/emailStyle.css")+"</style>";
	message += "<div class='mainWrapper'>";
	message += "<h1 class='header'>"+locationName+" Forecast</h1>";
	message += "<div class='subheader'>From Thor Weather</div>";
	message += "<br>";

	message += "<div class='forecastWrapper'>";

	message += "<div class='summaryWrapper'>";
	let weather = forecastJson.daily[0].weather[0].description.replace(/^\w/,c => c.toUpperCase());
	message += "<div class='weatherWrapper'>Weather: "+weather;
	message += "</div>";
	message += "<div class='tempMinMaxWrapper'>"
	let tempMax = Math.round(parseFloat(forecastJson.daily[0].temp.max));
	message += "<div class='tempWrapper'>";
	message += "High: "+String(tempMax)+"C";
	let tempMin = Math.round(parseFloat(forecastJson.daily[0].temp.min));
	message += "</div><div class='tempWrapper'>";
	message += "Low: "+String(tempMin)+"C";
	message += "</div>";
	message += "</div>";
	let rainChance = Math.round(parseFloat(forecastJson.daily[0].pop) * 100);
	message += "<div class='rainWrapper'>";
	message += "Chance of rain: "+String(rainChance)+"%";
	message += "</div>";
	message += "</div></div>"

	message += "<div class='hourlyWrapper'>";
	message += "<h3 class='hourlyHeader'>Hourly Temperature</h3>";
	message += "<table class='hourlyTable'>";
	message += "<tr class='hourlyRow'><th>Hour</th><th>Temperature</th><th>Feels like</th></tr>";
	let hours = forecastJson.hourly.slice(0,24);
	hours.forEach((elem,i) => {
		//todo: time zones
		let date = new Date(elem.dt*1000);
		let hour = String(date.getHours())+":00";
		if(date.getHours()<10) hour = "0"+hour;
		message += "<tr class='hourlyRow'><td>";
		message += hour;
		message += "</td><td>";
		message += Math.round(elem.temp)+"C";
		message += "</td><td>";
		message += Math.round(elem.feels_like)+"C";
		message += "</td></tr>";
	});
	message += "</table>";
	message += "</div>";

	message += "<div class='detailsWrapper'>";
	let sunrise = new Date(forecastJson.daily[0].sunrise*1000);
	let sunset;
	let pressure;
	let humidity;
	let dewPoint;
	let windSpeed;
	let windDir;
	let clouds;
	let uv;
	message += "</div>";

	message += "</div></div>";
	return message;
}
