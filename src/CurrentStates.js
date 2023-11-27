import React, { useState } from "react";
import { Divider, Table, Space, Button } from "antd";

export default function CurrentStates() {
  const current_states = cszData.current_custom_zones;
  const formattedData = [];

  Object.entries(current_states).forEach(([countryCode, states]) => {
    Object.entries(states).forEach(([stateCode, stateName]) => {
      formattedData.push({
        key: stateCode,
        country: countryCode,
        state: stateName,
        code: stateCode,
      });
    });
  });

  const columns = [
    {
      title: "Country",
      dataIndex: "country",
      key: "country",
    },
    {
      title: "State",
      dataIndex: "state",
      key: "state",
    },
    {
      title: "Code",
      dataIndex: "code",
      key: "code",
    },
    {
      title: "Action",
      key: "action",
      render: (_, record) => (
        <Space size="middle">
          <Button onClick={() => handleDelete(record.key)}>Delete</Button>
        </Space>
      ),
    },
  ];

  const handleDelete = (code) => {
    // Implement delete functionality here
    console.log("Delete state with code:", code);
  };

  return (
    <div>
      <Divider>Current custom shipping zones</Divider>
      <Table columns={columns} dataSource={formattedData} />
    </div>
  );
}
