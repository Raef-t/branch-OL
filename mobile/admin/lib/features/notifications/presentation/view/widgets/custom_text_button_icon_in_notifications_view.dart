import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';

class CustomTextButtonIconInNotificationsView extends StatelessWidget {
  const CustomTextButtonIconInNotificationsView({
    super.key,
    required this.onPressed,
    required this.text,
    required this.color,
    required this.image,
  });
  final void Function() onPressed;
  final String text;
  final Color color;
  final Image image;
  @override
  Widget build(BuildContext context) {
    return TextButton.icon(
      onPressed: onPressed,
      iconAlignment: IconAlignment.end,
      label: TextMedium14Component(text: text, color: color),
      icon: image,
    );
  }
}
