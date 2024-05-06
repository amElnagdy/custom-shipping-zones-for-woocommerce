import React, { useState } from "react";
import { Button, Result, Spin } from "antd";
import CountrySelector from "./CountrySelector";
import StateAdder from "./StateAdder";
import AddedStates from "./AddedStates";
import CurrentStates from "./CurrentStates";

const App = () => {
  const strings = cszStrings;
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
      });
  };

  const navigateToWooCommerceSettings = (
    <span>
      {strings.navigate_to_woocommerce_settings
        .split("WooCommerce → Settings → Shipping")
        .map((text, index) =>
          index === 0
            ? text
            : [
                <a key="link" href="admin.php?page=wc-settings&tab=shipping">
                  WooCommerce → Settings → Shipping
                </a>,
                text,
              ]
        )}
    </span>
  );

  if (savedSuccessfully) {
    return (
      <Result
        status="success"
        title={strings.states_saved_successfully}
        subTitle={<>{navigateToWooCommerceSettings}</>}
        extra={[
          <Button
            type="primary"
            key="console"
            onClick={() => window.location.reload()}
          >
            {strings.reload_page}
          </Button>,
        ]}
      />
    );
  }

  return (
    <Spin spinning={loading}>
      <div
        style={{
          margin: "0 auto",
          border: "1px solid #e6e6e6",
          padding: "10px 15px",
        }}
      >
        <CountrySelector
          setSelectedCountry={setSelectedCountry}
          addedStates={addedStates}
          strings={strings}
        />
        {selectedCountry && (
          <StateAdder
            selectedCountry={selectedCountry}
            setAddedStates={setAddedStates}
            strings={strings}
          />
        )}
        {addedStates.length > 0 && (
          <>
            <AddedStates
              addedStates={addedStates}
              selectedCountry={selectedCountry}
              setAddedStates={setAddedStates}
              strings={strings}
            />
            <Button
              style={{ marginTop: "20px" }}
              onClick={handleSaveStates}
              type="primary"
            >
              {strings.save_states}
            </Button>
          </>
        )}
        <div
          style={{
            padding: "40px 25px",
            marginTop: "30px",
          }}
        >
          <CurrentStates strings={strings} />
        </div>
        
      </div>
    </Spin>
  );
};

export default App;
