
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "@/components/ui/use-toast";
import { CodeGenerator } from "@/components/CodeGenerator";

export default function Index() {
  return (
    <main className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-6 text-center">PHP File Generator</h1>
      <p className="text-center mb-8 text-gray-600">Create and download PHP files easily</p>
      
      <div className="max-w-3xl mx-auto">
        <CodeGenerator />
      </div>
    </main>
  );
}
