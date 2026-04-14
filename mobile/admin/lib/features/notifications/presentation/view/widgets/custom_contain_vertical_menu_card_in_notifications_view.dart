import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/features/notifications/presentation/view/widgets/custom_text_button_icon_in_notifications_view.dart';
import '/gen/assets.gen.dart';

class CustomContainVerticalMenuCardInNotificationsView extends StatelessWidget {
  const CustomContainVerticalMenuCardInNotificationsView({
    super.key,
    required this.readedOnPressed,
    required this.deleteOnPressed,
  });
  final void Function() readedOnPressed, deleteOnPressed;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CustomTextButtonIconInNotificationsView(
          onPressed: deleteOnPressed,
          text: 'حذف',
          color: ColorsStyle.mediumRedColor,
          image: Assets.images.deleteImage.image(),
        ),
        CustomTextButtonIconInNotificationsView(
          onPressed: readedOnPressed,
          text: 'مقروء',
          color: ColorsStyle.mediumBlackColor2,
          image: Assets.images.readedImage.image(),
        ),
      ],
    );
  }
}
