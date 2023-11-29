import React, { useState } from "react";
import { Select, Typography } from "antd";

export default function CountrySelector({ setSelectedCountry, addedStates }) {
  const { Title } = Typography;
  const countries = cszData.countries.data;
  const [country, setCountry] = useState("");

  const handleChange = (value) => {
    setCountry(value);
    setSelectedCountry(value);
  };

  return (
    <>
      <Title level={4}>Select a Country</Title>
      <Select
        showSearch
        style={{ width: 300 }}
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
    </>
  );
}
