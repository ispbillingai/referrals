
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Textarea } from "@/components/ui/textarea";
import { Input } from "@/components/ui/input";
import { toast } from "@/components/ui/use-toast";

export function CodeGenerator() {
  const [phpCode, setPhpCode] = useState(`<?php\n\necho "Hello, World!";\n\n?>`);
  const [fileName, setFileName] = useState("myfile.php");

  const handleDownload = () => {
    try {
      // Create a blob with the text content
      const blob = new Blob([phpCode], { type: "application/x-httpd-php" });
      
      // Create a URL for the blob
      const url = URL.createObjectURL(blob);
      
      // Create a temporary anchor element to trigger download
      const a = document.createElement("a");
      a.href = url;
      a.download = fileName.endsWith(".php") ? fileName : `${fileName}.php`;
      
      // Append to the document, click, and clean up
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      
      // Revoke the URL to free up memory
      URL.revokeObjectURL(url);
      
      toast({
        title: "Success!",
        description: `Your PHP file "${a.download}" has been downloaded.`,
      });
    } catch (error) {
      console.error("Download failed:", error);
      toast({
        title: "Download failed",
        description: "There was an error creating your PHP file.",
        variant: "destructive",
      });
    }
  };

  return (
    <Card className="p-6 shadow-lg">
      <div className="mb-4">
        <label htmlFor="filename" className="block text-sm font-medium mb-2">
          File Name
        </label>
        <Input
          id="filename"
          value={fileName}
          onChange={(e) => setFileName(e.target.value)}
          placeholder="Enter file name (e.g., myfile.php)"
          className="w-full"
        />
      </div>
      
      <div className="mb-4">
        <label htmlFor="phpcode" className="block text-sm font-medium mb-2">
          PHP Code
        </label>
        <Textarea
          id="phpcode"
          value={phpCode}
          onChange={(e) => setPhpCode(e.target.value)}
          placeholder="Enter your PHP code here..."
          className="w-full h-64 font-mono"
        />
      </div>
      
      <Button onClick={handleDownload} className="w-full">
        Download PHP File
      </Button>
    </Card>
  );
}
