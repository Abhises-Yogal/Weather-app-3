const fetchData = async (cityName) => {
    if (!cityName) {
        alert("Please enter a city name!");
        return;
    }

    const apiUrl = `https://abhisesprototype2.infy.uk/prototype2/connection.php?q=${cityName}`;
    let data;
    try {
        if (navigator.onLine) {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error("Error fetching data........");
            }
            data = await response.json();
            if (data.length === 0) {
            throw new Error("No weather data is available for the entered city.");
            }
            // Save data to localStorage
            localStorage.setItem(cityName, JSON.stringify(data));
            //console.log("Data saved to localStorage for:", cityName);
        } else {
            // Offline: Retrieve data from localStorage
            data = JSON.parse(localStorage.getItem(cityName));
            if (!data) {
                throw new Error("No cached data available for this city.");
            }
            //console.log("Data retrieved from localStorage for:", cityName);
        }

        const weatherInfo = data[0];
        const dateTime = new Date(weatherInfo.DateAndTime * 1000).toLocaleString();
        let icon = weatherInfo.Icon;

        document.getElementById("weatherIcon").innerHTML = `<img class="weathericon" src="https://openweathermap.org/img/wn/${icon}@2x.png" alt="Weather icon">`;
        document.getElementById("cityName").innerHTML = `${weatherInfo.city}, ${weatherInfo.Country}`;
        document.getElementById("dateAndTime").innerHTML = dateTime;
        document.getElementById("weatherStatus").innerHTML = weatherInfo.Weather_Status;
        document.getElementById("weatherDescription").innerHTML = weatherInfo.Weather_Description;
        document.getElementById("temperatureMax").innerHTML = `Max temp: ${weatherInfo.MaxTemp}°C`;
        document.getElementById("temperatureMin").innerHTML = `Min temp: ${weatherInfo.MinTemp}°C`;
        document.getElementById("pressure").innerHTML = `Pressure: ${weatherInfo.Pressure} hPa`;
        document.getElementById("humidity").innerHTML = `Humidity: ${weatherInfo.Humidity}%`;
        document.getElementById("windSpeed").innerHTML = `Wind Speed: ${weatherInfo.Windspeed} m/s`;
    } catch (err) {
        alert(err.message);
        console.error("Error fetching weather data:", err);
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const defaultCity = "Cardiff";
    fetchData(defaultCity); 
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById("btn").addEventListener("click", () => {
        fetchData(document.getElementById("search").value.trim());
        document.getElementById("search").value = "";
    });
});