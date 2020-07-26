const mysql = require("mysql");
const fs = require("fs");

//fetch constants
const vars = JSON.parse(fs.readFileSync("./emailConst.json"));

//connect to the database
let con = mysql.createConnection({
	host: vars.SQL_HOST,
	user: vars.SQL_USER,
	password: vars.SQL_PASS
});
console.log("Connecting to the database");
con.connect((err)=>{
	if(err) throw err;
	//connection was successful
	console.log("Querying the database");
	let query = "SELECT * FROM locations JOIN Account ON Account.lid=locations.user_lid";
	query += " WHERE locations.is_subscribed='YES'";
	con.query(query,(err,res)=>{
		if(err) throw err;
		console.log(res);
	});
});
