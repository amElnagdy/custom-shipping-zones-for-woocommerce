import React from "react";
import { Button, Form, Input } from "antd";

export default function StateAdder({ selectedCountry, setAddedStates }) {
  const [form] = Form.useForm();

  const onFinish = (values) => {
    setAddedStates((prevStates) => [...prevStates, values.stateName]);
    form.resetFields();
  };

  return (
    <Form form={form} layout="inline" onFinish={onFinish} autoComplete="off">
      <Form.Item
        name="stateName"
        rules={[{ required: true, message: "Please input a state name!" }]}
      >
        <Input placeholder="State Name" />
      </Form.Item>
      <Form.Item>
        <Button type="primary" htmlType="submit">
          Add State
        </Button>
      </Form.Item>
    </Form>
  );
}
