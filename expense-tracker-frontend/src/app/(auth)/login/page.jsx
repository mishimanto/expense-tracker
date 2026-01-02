"use client";

import Input from "@/components/UI/Input";
import Button from "@/components/UI/Button";
import Link from "next/link";

export default function LoginPage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-black px-4">
      <div className="w-full max-w-md bg-gray-950 border border-gray-800 rounded-xl p-8">
        
        <h1 className="text-2xl font-bold text-white text-center mb-2">
          Sign in to your account
        </h1>

        <p className="text-gray-400 text-sm text-center mb-6">
          Or{" "}
          <Link href="/register" className="text-blue-500 hover:underline">
            create a new account
          </Link>
        </p>

        <form className="space-y-4">
          <Input label="Email address *" type="email" />
          <Input label="Password *" type="password" />

          <Button>Sign in</Button>
        </form>

        <div className="mt-6 text-sm text-gray-400">
          <p className="font-semibold mb-1">Demo Credentials</p>
          <p>Admin: admin@example.com / password</p>
          <p>User: user@example.com / password</p>
        </div>
      </div>
    </div>
  );
}
