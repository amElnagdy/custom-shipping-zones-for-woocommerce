import React from "react";
import { Button, Form, Input } from "antd";

export default function StateAdder({
  selectedCountry,
  setAddedStates,
  strings,
}) {
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
    <Form
      form={form}
      layout="horizontal"
      onFinish={onFinish}
      autoComplete="off"
    >
      <Form.Item
        name="stateName"
        rules={[{ required: true, message: strings.please_enter_state_name }]}
        style={{
          display: "inline-block",
          width: "calc(30% - 12px)",
          marginTop: "20px",
          marginRight: "20px",
        }}
      >
        <Input
          placeholder={strings.state_name}
          onChange={onStateNameChange}
          autoFocus
        />
      </Form.Item>
      <Form.Item
        name="stateCode"
        help={strings.state_codes_are_auto_generated}
        style={{
          display: "inline-block",
          width: "calc(30% - 12px)",
          marginTop: "20px",
        }}
      >
        <Input placeholder={strings.state_code} disabled />
      </Form.Item>
      <Form.Item>
        <Button type="dashed" htmlType="submit">
          {strings.add_state}
        </Button>
      </Form.Item>
    </Form>
  );
}
