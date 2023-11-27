import React, { useState } from "react";
import { Button, Result, Spin } from "antd";
import CountrySelector from "./CountrySelector";
import StateAdder from "./StateAdder";
import AddedStates from "./AddedStates";
import CurrentStates from "./CurrentStates";

const App = () => {
  const [selectedCountry, setSelectedCountry] = useState("");
  const [addedStates, setAddedStates] = useState([]);
  const [loading, setLoading] = useState(false);
  const [savedSuccessfully, setSavedSuccessfully] = useState(false);

  const handleSaveStates = () => {
    setLoading(true);
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
      .then(() => {
        setLoading(false);
        setSavedSuccessfully(true);
        setTimeout(() => window.location.reload(), 2000);
      });
  };

  if (savedSuccessfully) {
    return (
      <Result
        status="success"
        title="States Saved Successfully!"
        subTitle="Your changes have been saved. The page will reload shortly."
        extra={[
          <Button
            type="primary"
            key="console"
            onClick={() => window.location.reload()}
          >
            Reload Page
          </Button>,
        ]}
      />
    );
  }

  return (
    <Spin spinning={loading}>
      <div
        style={{
          maxWidth: "950px",
          margin: "0 auto",
          border: "1px solid #e6e6e6",
          padding: "40px 25px",
          marginTop: "50px",
          backgroundColor: "#fff",
        }}
      >
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
            <Button
              style={{ marginTop: "20px" }}
              onClick={handleSaveStates}
              type="primary"
            >
              Save States
            </Button>
          </>
        )}
        <div
          style={{
            padding: "40px 25px",
            marginTop: "30px",
          }}
        >
          <CurrentStates />
        </div>
      </div>
    </Spin>
  );
};

export default App;
