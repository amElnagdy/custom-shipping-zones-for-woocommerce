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
          delete data[countryCode][stateCode];
          setData({ ...data });
          message.success({
            content: strings.state_deleted,
            duration: 1,
            style: {
              marginTop: "2vh",
            },
          });
        } else {
          message.error({
            content: strings.failed_to_delete_state,
            style: {
              marginTop: "2vh",
            },
          });
        }
      })
      .catch((error) => {
        console.error("Error deleting state:", error);
        message(strings.an_error_occurred);
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
