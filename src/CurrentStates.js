import React, { useState } from "react";
import { Divider, Table, Space, Button, message } from "antd";

export default function CurrentStates({ strings }) {
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
          const newStateData = { ...data };
          if (newStateData[countryCode]) {
            delete newStateData[countryCode][stateCode];
            setData(newStateData);
            message.success({
              content: strings.state_deleted,
              duration: 2,
              style: {
                marginTop: "3vh",
              },
            });
          }
        } else {
          const errorMessage = result.data === 'state_is_in_use'
            ? strings.state_is_in_use
            : strings.failed_to_delete_state;
      
          message.error({
            content: errorMessage,
            duration: 5,
            style: {
              marginTop: "5vh",
            },
          });
        }
      })
      .catch((error) => {
        console.error("Error deleting state:", error);
        message.error({
          content: strings.an_error_occurred,
          style: {
            marginTop: "2vh",
          },
        });
      });
  };

  const columns = [
    {
      title: strings.country,
      dataIndex: "country",
      key: "country",
    },
    {
      title: strings.state,
      dataIndex: "state",
      key: "state",
    },
    {
      title: strings.code,
      dataIndex: "code",
      key: "code",
    },
    {
      title: strings.action,
      key: "action",
      render: (_, record) => (
        <Space size="middle">
          <Button onClick={() => handleDelete(record.code, record.country)}>
            {strings.delete}
          </Button>
        </Space>
      ),
    },
  ];

  return (
    <div>
      <Divider>{strings.current_custom_shipping_zones}</Divider>
      <Table columns={columns} dataSource={formattedData} />
    </div>
  );
}
