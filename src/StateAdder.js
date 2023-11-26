import React from "react";
import { Button, Form, Input } from "antd";

export default function StateAdder({ selectedCountry, setAddedStates }) {
  const [form] = Form.useForm();

  const generateStateCode = (stateName) => {
    const initials = stateName.match(/\b\w/g) || [];
    const code = (
      (initials.shift() || "") + (initials.pop() || "")
    ).toUpperCase();
    return (
      selectedCountry + "-" + code + "-" + Math.floor(100 + Math.random() * 900)
    );
  };

  const onFinish = (values) => {
    const state = {
      name: values.stateName,
      code: values.stateCode || generateStateCode(values.stateName),
    };
    setAddedStates((prevStates) => [...prevStates, state]);
    form.resetFields();
  };

  const onStateNameChange = (e) => {
    form.setFieldsValue({ stateCode: generateStateCode(e.target.value) });
  };

  return (
    <Form form={form} layout="vertical" onFinish={onFinish} autoComplete="off">
      <Form.Item
        name="stateName"
        rules={[{ required: true, message: "Please enter a state name" }]}
      >
        <Input
          placeholder="State Name"
          onChange={onStateNameChange}
          autoFocus
        />
      </Form.Item>
      <Form.Item
        name="stateCode"
        rules={[{ required: true, message: "Please enter a state code" }]}
        help="Automatically generated."
      >
        <Input placeholder="State Code" disabled/>
      </Form.Item>
      <Form.Item>
        <Button type="primary" htmlType="submit">
          Add State
        </Button>
      </Form.Item>
    </Form>
  );
}
