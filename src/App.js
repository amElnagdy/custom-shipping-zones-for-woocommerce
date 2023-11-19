import React, { useState } from "react";
import CountrySelector from "./CountrySelector";
import StateAdder from "./StateAdder";
import AddedStates from "./AddedStates";

const App = () => {
  const [selectedCountry, setSelectedCountry] = useState("");
  const [addedStates, setAddedStates] = useState([]);

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
        <AddedStates
          addedStates={addedStates}
          selectedCountry={selectedCountry}
        />
      )}
    </div>
  );
};

export default App;
