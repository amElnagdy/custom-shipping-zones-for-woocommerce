import React, { useState } from "react";
import { Divider, Table, Space, Button, message, Result } from "antd";

export default function CurrentStates() {
  const current_states = cszData.current_custom_zones;
  const [data, setData] = useState(current_states);

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

  const handleDelete = (stateCode, countryCode) => {
    const formData = new URLSearchParams();
    formData.append("action", "csz_delete_state");
    formData.append("nonce", cszAjax.nonce);
    formData.append("countryCode", countryCode);
    formData.append("stateCode", stateCode);

    fetch(cszAjax.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formData,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          delete data[countryCode][stateCode];
          setData({ ...data });
          message.success({
            content: "State deleted successfully",
            duration: 1,
            style: {
              marginTop: "2vh",
            },
          });
        } else {
          message.error({
            content: "Failed to delete state",
            style: {
              marginTop: "2vh",
            },
          });
        }
      })
      .catch((error) => {
        console.error("Error deleting state:", error);
        message("An error occurred while deleting the state");
      });
  };

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
          <Button onClick={() => handleDelete(record.code, record.country)}>
            Delete
          </Button>
        </Space>
      ),
    },
  ];

  return (
    <div>
      <Divider>Current custom shipping zones</Divider>
      <Table columns={columns} dataSource={formattedData} />
    </div>
  );
}
