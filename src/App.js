import React from "react";

const App = () => {
	let today = new Date();
	let date =
		today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate();
	let time =
		today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	let dateTime = date + " " + time;
	return (
		<div>
			<h4>{dateTime}</h4>
		</div>
	);
};

export default App;
