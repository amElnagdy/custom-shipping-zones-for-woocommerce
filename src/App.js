import React, { useState } from "react";
import { Button } from "antd";
import CountrySelector from "./CountrySelector";
import StateAdder from "./StateAdder";
import AddedStates from "./AddedStates";

const App = () => {
  const [selectedCountry, setSelectedCountry] = useState("");
  const [addedStates, setAddedStates] = useState([]);

  const handleSaveStates = () => {
    const formData = new URLSearchParams();
    formData.append("action", "csz_save_states");
    formData.append("nonce", cszAjax.nonce);
    formData.append("countryCode", selectedCountry);
    formData.append("states", JSON.stringify(addedStates));
    fetch(cszAjax.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => console.log(data));
  };

  return (
    <div className="csz">
      <CountrySelector
        setSelectedCountry={setSelectedCountry}
        addedStates={addedStates}
      />
      {selectedCountry && (
        <StateAdder
          selectedCountry={selectedCountry}
          setAddedStates={setAddedStates}
        />
      )}
      {addedStates.length > 0 && (
        <>
          <AddedStates
            addedStates={addedStates}
            selectedCountry={selectedCountry}
            setAddedStates={setAddedStates}
          />
          <Button onClick={handleSaveStates}>Save States</Button>
        </>
      )}
    </div>
  );
};

export default App;
