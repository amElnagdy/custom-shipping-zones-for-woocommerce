import React from "react";
import { Divider, List, Button } from "antd";

export default function AddedStates({
  addedStates,
  selectedCountry,
  setAddedStates,
  strings,
}) {
  const handleDelete = (stateCode) => {
    setAddedStates(addedStates.filter((state) => state.code !== stateCode));
  };

  return (
    <div>
      <Divider>{`${strings.these_states_will_be_added} ${selectedCountry}`}</Divider>
      <List
        size="small"
        bordered
        dataSource={addedStates}
        renderItem={(state) => (
          <List.Item
            actions={[
              <Button type="link" onClick={() => handleDelete(state.code)}>
                {strings.delete}
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
