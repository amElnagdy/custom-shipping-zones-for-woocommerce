import React, { useState } from "react";
import { Select } from "antd";

export default function CountrySelector({ setSelectedCountry, addedStates }) {
  const countries = cszData.states.data;
  const [country, setCountry] = useState("");

  const handleChange = (value) => {
    setCountry(value);
    setSelectedCountry(value);
  };

  return (
    <Select
      showSearch
      style={{ width: 200 }}
      placeholder="Select a country"
      optionFilterProp="children"
      onChange={handleChange}
      filterOption={(input, option) =>
        option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
      }
      value={country}
      disabled={addedStates.length > 0}
    >
      {Object.entries(countries).map(([countryCode, countryName]) => (
        <Select.Option key={countryCode} value={countryCode}>
          {countryName}
        </Select.Option>
      ))}
    </Select>
  );
}
