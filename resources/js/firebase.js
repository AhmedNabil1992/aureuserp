import { getApp, getApps, initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";
import * as firebaseDatabase from "firebase/database";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID,
};

const hasRequiredFirebaseConfig = Boolean(
    firebaseConfig.apiKey && firebaseConfig.projectId && firebaseConfig.appId,
);

export const firebaseApp = hasRequiredFirebaseConfig
    ? getApps().length
        ? getApp()
        : initializeApp(firebaseConfig)
    : null;

export const firebaseDb = firebaseApp ? getDatabase(firebaseApp) : null;

export function getFirebaseApp(name = "[DEFAULT]") {
    if (!hasRequiredFirebaseConfig) {
        return null;
    }

    const app = getApps().find((existingApp) => existingApp.name === name);

    if (app) {
        return app;
    }

    return name === "[DEFAULT]"
        ? initializeApp(firebaseConfig)
        : initializeApp(firebaseConfig, name);
}

export function getFirebaseDatabase(name = "[DEFAULT]") {
    const app = getFirebaseApp(name);

    return app ? getDatabase(app) : null;
}

window.AureusFirebase = {
    firebaseConfig,
    hasRequiredFirebaseConfig,
    getApp: getFirebaseApp,
    getDatabase: getFirebaseDatabase,
};

window.firebaseDatabase = firebaseDatabase;

export { firebaseConfig, hasRequiredFirebaseConfig };
