import 'package:flutter/material.dart';
import '/core/components/card_and_two_texts_component.dart';
import '/gen/assets.gen.dart';

class ManyOfCardAndTwoTextsComponent extends StatelessWidget {
  const ManyOfCardAndTwoTextsComponent({
    super.key,
    required this.messageOnTap,
    required this.attendanceOnTap,
    required this.paymentOnTap,
    required this.workHourOnTap,
    required this.markOnTap,
  });
  final void Function() messageOnTap,
      attendanceOnTap,
      paymentOnTap,
      workHourOnTap,
      markOnTap;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceAround,
      children: [
        GestureDetector(
          onTap: messageOnTap,
          child: CardAndTwoTextsComponent(
            firstText: 'رسالة',
            secondText: 'نصية',
            imageProvider: Assets.images.purpleMessagesImage.provider(),
          ),
        ),
        GestureDetector(
          onTap: attendanceOnTap,
          child: CardAndTwoTextsComponent(
            firstText: '/غياب',
            secondText: 'حضور',
            imageProvider: Assets.images.presentOrAbsentImage.provider(),
          ),
        ),
        GestureDetector(
          onTap: paymentOnTap,
          child: CardAndTwoTextsComponent(
            firstText: 'الدفعات',
            secondText: 'المالية',
            imageProvider: Assets.images.paymentsImage.provider(),
          ),
        ),
        GestureDetector(
          onTap: workHourOnTap,
          child: CardAndTwoTextsComponent(
            firstText: 'برنامج',
            secondText: 'الدوام',
            imageProvider: Assets.images.purpleWorldImage.provider(),
          ),
        ),
        GestureDetector(
          onTap: markOnTap,
          child: CardAndTwoTextsComponent(
            firstText: 'علامات',
            secondText: '',
            imageProvider: Assets.images.marksImage.provider(),
          ),
        ),
      ],
    );
  }
}
