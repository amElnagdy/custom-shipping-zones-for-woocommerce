import React from "react";
import { Divider, List, Button } from "antd";

export default function AddedStates({
  addedStates,
  selectedCountry,
  setAddedStates,
}) {
  const handleDelete = (stateCode) => {
    setAddedStates(addedStates.filter((state) => state.code !== stateCode));
  };

  return (
    <div>
      <Divider>These states will be added to '{selectedCountry}'</Divider>
      <List
        size="small"
        bordered
        dataSource={addedStates}
        renderItem={(state) => (
          <List.Item
            actions={[
              <Button type="link" onClick={() => handleDelete(state.code)}>
                Delete
              </Button>,
            ]}
          >
            {state.name} ({state.code})
          </List.Item>
        )}
      />
    </div>
  );
}
