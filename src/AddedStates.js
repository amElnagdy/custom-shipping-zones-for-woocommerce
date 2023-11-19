import React from "react";
import { Divider, List } from "antd";

export default function AddedStates({ addedStates, selectedCountry }) {
  return (
    <div>
      <Divider>These states will be added to ' {selectedCountry}</Divider>
      <List
        size="small"
        bordered
        dataSource={addedStates}
        renderItem={(state) => <List.Item>{state}</List.Item>}
      />
    </div>
  );
}
