import React, { useState, useEffect } from "react";
import { Button, message, Modal, Upload, Divider } from "antd";
import { UploadOutlined } from "@ant-design/icons";

const ExportImport = ({ strings, data }) => {
  const [file, setFile] = useState(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [unsavedChanges, setUnsavedChanges] = useState(true);

  useEffect(() => {
    const handleBeforeUnload = (event) => {
      if (unsavedChanges) {
        event.preventDefault();
        event.returnValue = "";
      }
    };
    window.addEventListener("beforeunload", handleBeforeUnload);
    return () => {
      window.removeEventListener("beforeunload", handleBeforeUnload);
    };
  }, [unsavedChanges]); 

  const beforeUpload = (file) => {
    setFile(file);
    setIsModalVisible(true);
    return false;
  };

  /**
   * Export the current custom shipping zones.
   */

  const handleExport = () => {
    if (
      !cszData.current_custom_zones ||
      Object.keys(cszData.current_custom_zones).length === 0
    ) {
      message.error({
        content: strings.no_custom_zones_to_export,
        style: {
          marginTop: "2vh",
        },
      });
      return;
    }

    // Prepare file name: siteTitle_date_custom_shipping_zones.json
    const date = new Date().toISOString().slice(0, 10);
    const safeTitle = cszData.siteTitle
      .replace(/[^a-z0-9]/gi, "_")
      .toLowerCase();
    const fileName = `${safeTitle}_${date}_custom_shipping_zones.json`;

    const dataStr = JSON.stringify(cszData.current_custom_zones);
    const blob = new Blob([dataStr], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    link.remove();

    message.success({
      content: strings.custom_zones_exported,
      duration: 3,
      style: {
        marginTop: "5vh",
      },
    });
  };

  /**
   * Import the custom shipping zones from the uploaded file.
   */

  const handleImport = () => {
    if (!file) return;
    setIsModalVisible(false);

    const reader = new FileReader();
    reader.onload = async (e) => {
      const jsonData = e.target.result;
      const formData = new FormData();
      formData.append("action", "csz_import_states");
      formData.append("nonce", cszAjax.nonce);
      formData.append("jsonData", jsonData);

      try {
        const response = await fetch(cszAjax.ajax_url, {
          method: "POST",
          body: formData,
        });
        const data = await response.json();

        if (data.success) {
            setUnsavedChanges(false);
            setTimeout(() => {
              window.location.reload();
            }, 0);
          }  else {
          message.error({
            content: strings.error_importing_zones,
            duration: 3,
            style: {
              marginTop: "5vh",
            },
          });
        }
      } catch (error) {
        message.error({
          content: strings.error_importing_zones,
          duration: 3,
          style: {
            marginTop: "5vh",
          },
        });
      }
    };
    reader.readAsText(file);
  };

  return (
    <>
      <Divider>{strings.export_import_divider}</Divider>
      <div style={{ marginBottom: 16 }}>
        <p>{strings.export_description}</p>
      </div>
      <Button
        type="default"
        onClick={handleExport}
        disabled={!data || !data.length}
      >
        {strings.export_custom_zones}
      </Button>
      <Divider>{strings.or}</Divider>

      <Upload beforeUpload={beforeUpload} showUploadList={false}>
        <Button icon={<UploadOutlined />}>{strings.import_custom_zones}</Button>
      </Upload>
      <Modal
        title={strings.import_custom_zones}
        open={isModalVisible}
        onOk={handleImport}
        onCancel={() => setIsModalVisible(false)}
      >
        <p>{strings.import_warning}</p>
      </Modal>
    </>
  );
};

export default ExportImport;
